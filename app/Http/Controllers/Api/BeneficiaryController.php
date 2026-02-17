<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Beneficiary::query();

        if ($user->role === 'volunteer') {
            $query->where('submitted_by', $user->id);
        }
        elseif ($user->role === 'manager' || $user->role === 'organization_admin') {
            if ($user->role === 'organization_admin') {
                $query->whereHas('project', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                });
            }
            else {
                $query->whereHas('project.assignedUsers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->with(['project', 'packages', 'documents'])->paginate(15));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'government_id' => 'required|unique:beneficiaries,government_id',
            'assigned_project_id' => 'required|exists:projects,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $beneficiary = Beneficiary::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'government_id' => $request->government_id,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'assigned_project_id' => $request->assigned_project_id,
                'submitted_by' => $request->user()->id,
                'status' => 'submitted',
            ]);

            if ($request->has('package_ids')) {
                $beneficiary->packages()->attach($request->package_ids);
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'create',
                'description' => 'Submitted beneficiary ' . $beneficiary->id,
                'subject_type' => Beneficiary::class ,
                'subject_id' => $beneficiary->id,
                'ip_address' => $request->ip(),
            ]);

            // Send notifications to managers and admins
            $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
            $recipients = User::whereIn('role', ['manager', 'organization_admin', 'super_admin'])
                ->where('id', '!=', $request->user()->id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            Notification::sendToMany(
                $recipients,
                'beneficiary_submitted',
                'New Beneficiary Submitted',
                $fullName . ' has been submitted by ' . $request->user()->name . ' for review.',
            ['subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );

            DB::commit();
            return response()->json($beneficiary, 201);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function uploadDocument(Request $request, $beneficiaryId)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,pdf|max:5120',
            'type' => 'required|string',
        ]);

        $beneficiary = Beneficiary::findOrFail($beneficiaryId);

        if ($request->user()->id !== $beneficiary->submitted_by && !$request->user()->hasRole('manager')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $path = $request->file('file')->store('beneficiary_docs');

        $doc = BeneficiaryDocument::create([
            'beneficiary_id' => $beneficiary->id,
            'type' => $request->type,
            'file_path' => $path,
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json($doc, 201);
    }

    public function show($id)
    {
        $beneficiary = Beneficiary::with(['project.program.organization', 'submitter', 'packages', 'documents', 'reviews.manager'])
            ->findOrFail($id);

        return response()->json($beneficiary);
    }

    public function review(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,fraud',
            'remarks' => 'required_if:action,reject,fraud|string|nullable',
        ]);

        $beneficiary = Beneficiary::findOrFail($id);

        if (!in_array($request->user()->role, ['manager', 'organization_admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $statusMap = ['approve' => 'approved', 'reject' => 'rejected', 'fraud' => 'fraud'];

        \App\Models\Review::create([
            'beneficiary_id' => $beneficiary->id,
            'manager_id' => $request->user()->id,
            'action' => $request->action,
            'remarks' => $request->remarks,
        ]);

        $beneficiary->update(['status' => $statusMap[$request->action]]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => $request->action,
            'description' => ucfirst($request->action) . ' beneficiary #' . $beneficiary->id,
            'subject_type' => Beneficiary::class ,
            'subject_id' => $beneficiary->id,
            'ip_address' => $request->ip(),
        ]);

        // Send review notification to the submitter
        $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
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

        if ($beneficiary->submitted_by && $beneficiary->submitted_by !== $request->user()->id) {
            Notification::send(
                $beneficiary->submitted_by,
                $typeMap[$request->action],
                $titleMap[$request->action],
                $fullName . ' has been ' . $statusMap[$request->action] . ' by ' . $request->user()->name . '.',
            ['subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );
        }

        // For fraud: notify all admins
        if ($request->action === 'fraud') {
            $adminIds = User::whereIn('role', ['super_admin', 'organization_admin'])
                ->where('id', '!=', $request->user()->id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            Notification::sendToMany(
                $adminIds,
                'fraud_flagged',
                'Fraud Alert - Immediate Action Required',
                '⚠️ ' . $fullName . ' has been flagged as fraud by ' . $request->user()->name . '.',
            ['subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );
        }

        return response()->json(['message' => 'Beneficiary ' . $statusMap[$request->action] . ' successfully.']);
    }
}
