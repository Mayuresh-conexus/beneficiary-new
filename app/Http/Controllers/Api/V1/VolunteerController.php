<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Package;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    /**
     * GET /api/v1/volunteer/projects — Get assigned projects for volunteer
     */
    public function projects(Request $request)
    {
        $user = $request->user();

        $projects = $user->assignedProjects()
            ->with(['program.organization', 'packages'])
            ->withCount([
            'beneficiaries',
            'beneficiaries as approved_count' => fn($q) => $q->where('status', 'approved'),
            'beneficiaries as pending_count' => fn($q) => $q->whereIn('status', ['submitted', 'under_review']),
            'beneficiaries as rejected_count' => fn($q) => $q->where('status', 'rejected'),
        ])
            ->get()
            ->map(function ($project) {
            $total = $project->beneficiaries_count ?: 1;
            return [
            'id' => $project->id,
            'name' => $project->name,
            'location' => $project->location,
            'status' => $project->status,
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'organization' => $project->program->organization->name ?? null,
            'program' => $project->program->name ?? null,
            'packages' => $project->packages->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'type' => $p->type,
            'value' => $p->value,
            'frequency' => $p->frequency,
            ]),
            'stats' => [
            'total' => $project->beneficiaries_count,
            'approved' => $project->approved_count,
            'pending' => $project->pending_count,
            'rejected' => $project->rejected_count,
            ],
            'progress' => round(($project->approved_count / max($total, 1)) * 100),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    /**
     * GET /api/v1/project/{id}/packages — Get packages for a project
     */
    public function projectPackages(Request $request, $id)
    {
        $project = Project::with('packages')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $project->packages->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'type' => $p->type,
        'value' => $p->value,
        'frequency' => $p->frequency,
        ]),
        ]);
    }

    /**
     * GET /api/v1/volunteer/dashboard — Dashboard stats for volunteer
     */
    public function dashboard(Request $request)
    {
        $userId = $request->user()->id;

        $assigned = $request->user()->assignedProjects()->count();
        $submitted = Beneficiary::where('submitted_by', $userId)->count();
        $approved = Beneficiary::where('submitted_by', $userId)->where('status', 'approved')->count();
        $pending = Beneficiary::where('submitted_by', $userId)->whereIn('status', ['submitted', 'under_review'])->count();
        $rejected = Beneficiary::where('submitted_by', $userId)->where('status', 'rejected')->count();
        $fraud = Beneficiary::where('submitted_by', $userId)->where('status', 'fraud')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'assigned_projects' => $assigned,
                'total_submissions' => $submitted,
                'approved' => $approved,
                'pending_review' => $pending,
                'rejected' => $rejected,
                'fraud_flagged' => $fraud,
            ],
        ]);
    }

    /**
     * GET /api/v1/sync-status — Check last sync and pending data
     */
    public function syncStatus(Request $request)
    {
        $userId = $request->user()->id;

        $lastSubmission = Beneficiary::where('submitted_by', $userId)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'last_sync' => $lastSubmission ? $lastSubmission->created_at->toIso8601String() : null,
                'server_time' => now()->toIso8601String(),
                'pending_submissions' => 0, // Server-side pending is always 0; local drafts are client-side
                'total_submitted' => Beneficiary::where('submitted_by', $userId)->count(),
            ],
        ]);
    }
}
