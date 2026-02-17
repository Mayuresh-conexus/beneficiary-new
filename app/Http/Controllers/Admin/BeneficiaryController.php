<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Review;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryController extends Controller
{
    public function create()
    {
        if (!auth()->user()->hasPermissionTo('create_beneficiaries')) {
            abort(403);
        }
        $projects = \App\Models\Project::with('program.organization')->get();
        return view('beneficiaries.create', compact('projects'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create_beneficiaries')) {
            abort(403);
        }
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'government_id' => 'required|string|unique:beneficiaries,government_id',
            'assigned_project_id' => 'required|exists:projects,id',
            'contact_number' => 'nullable|string',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'exists:packages,id',
        ]);

        \DB::beginTransaction();
        try {
            $beneficiary = Beneficiary::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'government_id' => $request->government_id,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'assigned_project_id' => $request->assigned_project_id,
                'submitted_by' => auth()->id(),
                'status' => 'submitted',
                'latitude' => 0,
                'longitude' => 0,
            ]);

            if ($request->has('package_ids')) {
                $beneficiary->packages()->attach($request->package_ids);
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'description' => 'Manually created beneficiary ' . $beneficiary->id,
                'subject_type' => Beneficiary::class ,
                'subject_id' => $beneficiary->id,
                'ip_address' => $request->ip(),
            ]);

            // Send notifications to managers and admins about new submission
            $this->notifyNewBeneficiary($beneficiary);

            \DB::commit();
            return redirect()->route('beneficiaries.index')->with('success', 'Beneficiary created successfully.');
        }
        catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create beneficiary: ' . $e->getMessage()])->withInput();
        }
    }

    public function index(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('view_beneficiaries')) {
            abort(403);
        }
        $query = Beneficiary::with(['project.program.organization', 'submitter', 'packages']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('government_id', 'like', "%$search%");
            });
        }

        $beneficiaries = $query->latest()->paginate(15);
        return view('beneficiaries.index', compact('beneficiaries'));
    }

    public function show(Beneficiary $beneficiary)
    {
        if (!auth()->user()->hasPermissionTo('view_beneficiaries')) {
            abort(403);
        }
        $beneficiary->load(['project.program.organization', 'submitter', 'packages', 'documents', 'reviews.manager']);
        $timeline = ActivityLog::where('subject_type', Beneficiary::class)
            ->where('subject_id', $beneficiary->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('beneficiaries.show', compact('beneficiary', 'timeline'));
    }

    public function review(Request $request, Beneficiary $beneficiary)
    {
        if (!auth()->user()->hasPermissionTo('review_beneficiaries')) {
            abort(403);
        }
        $request->validate([
            'action' => 'required|in:approve,reject,fraud',
            'remarks' => 'required_if:action,reject,fraud|string|nullable',
        ]);

        $action = $request->action;

        // Create review record
        Review::create([
            'beneficiary_id' => $beneficiary->id,
            'manager_id' => auth()->id(),
            'action' => $action,
            'remarks' => $request->remarks,
        ]);

        // Update status
        $statusMap = ['approve' => 'approved', 'reject' => 'rejected', 'fraud' => 'fraud'];
        $beneficiary->update(['status' => $statusMap[$action]]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => ucfirst($action) . ' beneficiary #' . $beneficiary->id . ($request->remarks ? ': ' . $request->remarks : ''),
            'subject_type' => Beneficiary::class ,
            'subject_id' => $beneficiary->id,
            'properties' => ['old_status' => $beneficiary->getOriginal('status'), 'new_status' => $statusMap[$action]],
            'ip_address' => $request->ip(),
        ]);

        // Send notifications about the review action
        $this->notifyReviewAction($beneficiary, $action, $request->remarks);

        $messages = [
            'approve' => 'Beneficiary approved successfully.',
            'reject' => 'Beneficiary rejected.',
            'fraud' => 'Beneficiary marked as fraud. Volunteer has been flagged.',
        ];

        return redirect()->route('beneficiaries.show', $beneficiary)->with('success', $messages[$action]);
    }

    /**
     * Notify managers/admins about a new beneficiary submission.
     */
    private function notifyNewBeneficiary(Beneficiary $beneficiary)
    {
        $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
        $link = route('beneficiaries.show', $beneficiary);

        // Notify all managers and admins
        $recipients = User::whereIn('role', ['manager', 'organization_admin', 'super_admin'])
            ->where('id', '!=', auth()->id())
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        Notification::sendToMany(
            $recipients,
            'beneficiary_submitted',
            'New Beneficiary Submitted',
            $fullName . ' has been submitted for review.',
        ['link' => $link, 'subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
        );
    }

    /**
     * Notify relevant users about a review action.
     */
    private function notifyReviewAction(Beneficiary $beneficiary, string $action, ?string $remarks)
    {
        $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
        $link = route('beneficiaries.show', $beneficiary);

        $typeMap = [
            'approve' => 'beneficiary_approved',
            'reject' => 'beneficiary_rejected',
            'fraud' => 'fraud_flagged',
        ];

        $titleMap = [
            'approve' => 'Beneficiary Approved',
            'reject' => 'Beneficiary Rejected',
            'fraud' => 'Fraud Alert',
        ];

        $messageMap = [
            'approve' => $fullName . ' has been approved by ' . auth()->user()->name . '.',
            'reject' => $fullName . ' has been rejected. ' . ($remarks ? 'Reason: ' . $remarks : ''),
            'fraud' => '⚠️ ' . $fullName . ' has been flagged as fraud by ' . auth()->user()->name . '. Immediate attention required.',
        ];

        // Notify the submitter (volunteer)
        if ($beneficiary->submitted_by && $beneficiary->submitted_by !== auth()->id()) {
            Notification::send(
                $beneficiary->submitted_by,
                $typeMap[$action],
                $titleMap[$action],
                $messageMap[$action],
            ['link' => $link, 'subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );
        }

        // For fraud: also notify super admins and org admins
        if ($action === 'fraud') {
            $adminIds = User::whereIn('role', ['super_admin', 'organization_admin'])
                ->where('id', '!=', auth()->id())
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            Notification::sendToMany(
                $adminIds,
                'fraud_flagged',
                'Fraud Alert - Immediate Action Required',
                $messageMap[$action],
            ['link' => $link, 'subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );
        }

        // For approvals: notify all super admins
        if ($action === 'approve') {
            $superAdminIds = User::where('role', 'super_admin')
                ->where('id', '!=', auth()->id())
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            Notification::sendToMany(
                $superAdminIds,
                'beneficiary_approved',
                $titleMap[$action],
                $messageMap[$action],
            ['link' => $link, 'subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );
        }
    }
}
