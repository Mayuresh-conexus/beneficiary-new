<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Organization;
use App\Models\Program;
use App\Models\Project;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $orgId = $request->organization_id;
        $programId = $request->program_id;
        $projectId = $request->project_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $query = Beneficiary::query();
        if ($orgId) {
            $query->whereHas('project', fn($q) => $q->where('organization_id', $orgId));
        }
        if ($programId) {
            $query->whereHas('project', fn($q) => $q->where('program_id', $programId));
        }
        if ($projectId) {
            $query->where('assigned_project_id', $projectId);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();
        $fraud = (clone $query)->where('status', 'fraud')->count();
        $pending = (clone $query)->whereIn('status', ['submitted', 'under_review'])->count();

        // Beneficiaries per project (for charts)
        $perProject = Project::withCount('beneficiaries')->get()->map(fn($p) => [
        'name' => $p->name,
        'count' => $p->beneficiaries_count,
        ]);

        // Package distribution
        $packageDist = DB::table('beneficiary_package')
            ->join('packages', 'packages.id', '=', 'beneficiary_package.package_id')
            ->select('packages.type', DB::raw('COUNT(*) as count'))
            ->groupBy('packages.type')
            ->get();

        // Volunteer performance
        $volunteerPerf = User::where('role', 'volunteer')
            ->withCount('submittedBeneficiaries')
            ->orderByDesc('submitted_beneficiaries_count')
            ->take(10)
            ->get();

        $organizations = Organization::all();
        $programs = Program::all();
        $projects = Project::all();

        return view('reports.index', compact(
            'total', 'approved', 'rejected', 'fraud', 'pending',
            'perProject', 'packageDist', 'volunteerPerf',
            'organizations', 'programs', 'projects'
        ));
    }

    public function exportCsv(Request $request)
    {
        $beneficiaries = Beneficiary::with(['project.program.organization', 'submitter', 'packages'])->get();

        $filename = 'beneficiaries_export_' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($beneficiaries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Gov ID', 'Status', 'Project', 'Organization', 'Submitted By', 'Date']);
            foreach ($beneficiaries as $b) {
                fputcsv($file, [
                    $b->id, $b->first_name, $b->last_name, $b->government_id, $b->status,
                    $b->project->name ?? 'N/A',
                    $b->project->organization->name ?? 'N/A',
                    $b->submitter->name ?? 'N/A',
                    $b->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
