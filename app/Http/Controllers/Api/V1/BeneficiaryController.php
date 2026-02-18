<?php

namespace App\Http\Controllers\Api\V1;

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
    /**
     * POST /api/v1/beneficiaries — Create new beneficiary
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'government_id' => 'required|string|unique:beneficiaries,government_id',
            'assigned_project_id' => 'required|exists:projects,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'exists:packages,id',
            // New Legacy Fields
            'contact_number_2' => 'nullable|string|max:30',
            'passport_number' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'beneficiary_type' => 'nullable|string',
            'no_of_households' => 'nullable|integer',
            'urgent_need' => 'nullable|string',
            'detail_situation' => 'nullable|string',
            'monthly_income' => 'nullable|numeric',
            'monthly_expenditure' => 'nullable|numeric',
            'debts' => 'nullable|numeric',
            'asset_value' => 'nullable|numeric',
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
                'biometric_template' => $request->biometric_template,
                'biometric_device' => $request->biometric_device,
                'biometric_enrolled_at' => $request->has('biometric_template') ? now() : null,
                'is_verified' => $request->has('biometric_template'),

                // Legacy Fields
                'contact_number_2' => $request->contact_number_2,
                'passport_number' => $request->passport_number,
                'employment_status' => $request->employment_status,
                'occupation' => $request->occupation,
                'beneficiary_type' => $request->beneficiary_type,
                'no_of_households' => $request->no_of_households ?? 1,
                'urgent_need' => $request->urgent_need,
                'detail_situation' => $request->detail_situation,
                'monthly_income' => $request->monthly_income,
                'monthly_expenditure' => $request->monthly_expenditure,
                'debts' => $request->debts,
                'asset_value' => $request->asset_value,
            ]);

            if ($request->has('package_ids')) {
                $beneficiary->packages()->attach($request->package_ids);
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'create',
                'description' => 'Submitted beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name,
                'subject_type' => Beneficiary::class ,
                'subject_id' => $beneficiary->id,
                'ip_address' => $request->ip(),
            ]);

            // Notify managers/admins
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

            return response()->json([
                'success' => true,
                'data' => $beneficiary->load(['project', 'packages']),
                'message' => 'Beneficiary submitted successfully',
            ], 201);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create beneficiary: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/beneficiaries/my-submissions — Volunteer's own submissions
     */
    public function mySubmissions(Request $request)
    {
        $query = Beneficiary::where('submitted_by', $request->user()->id)
            ->with(['project.program', 'packages']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('government_id', 'like', "%$search%");
            });
        }

        $beneficiaries = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $beneficiaries,
        ]);
    }

    /**
     * GET /api/v1/beneficiary/{id} — Show single beneficiary
     */
    public function show(Request $request, $id)
    {
        $beneficiary = Beneficiary::with([
            'project.program.organization',
            'submitter',
            'packages',
            'documents',
            'reviews.manager',
        ])->findOrFail($id);

        // Volunteers can only view their own submissions
        if ($request->user()->role === 'volunteer' && $beneficiary->submitted_by !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Build timeline
        $timeline = ActivityLog::where('subject_type', Beneficiary::class)
            ->where('subject_id', $beneficiary->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($log) => [
            'action' => $log->action,
            'description' => $log->description,
            'user' => $log->user ? $log->user->name : 'System',
            'timestamp' => $log->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $beneficiary,
            'timeline' => $timeline,
        ]);
    }

    /**
     * GET /api/v1/beneficiary/{id}/biometric — Get biometric template for verification
     */
    public function biometricData(Request $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

        // Security Check: Only allow if user is assigned to project or is admin?
        // For now, allow any authenticated user (volunteer) to fetch for verification.

        if (!$beneficiary->biometric_template) {
            return response()->json([
                'success' => false,
                'message' => 'No biometric data enrolled',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $beneficiary->id,
                'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name,
                'template' => $beneficiary->biometric_template,
                'device' => $beneficiary->biometric_device,
                'enrolled_at' => $beneficiary->biometric_enrolled_at,
            ],
        ]);
    }

    /**
     * POST /api/v1/beneficiary/{id}/verify-biometric — Log successful verification
     */
    public function logVerification(Request $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'verify',
            'description' => 'Biometric verification successful for ' . $beneficiary->first_name,
            'subject_type' => Beneficiary::class ,
            'subject_id' => $beneficiary->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * PUT /api/v1/beneficiary/{id} — Resubmit rejected beneficiary
     */
    public function update(Request $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

        // Only the original submitter can resubmit
        if ($beneficiary->submitted_by !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only rejected beneficiaries can be resubmitted
        if (!in_array($beneficiary->status, ['rejected', 'fraud'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only rejected or flagged beneficiaries can be resubmitted',
            ], 422);
        }

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'exists:packages,id',
        ]);

        DB::beginTransaction();
        try {
            $beneficiary->update(array_merge(
                $request->only([
                'first_name', 'last_name', 'contact_number',
                'address', 'date_of_birth', 'gender',
                'latitude', 'longitude',
            ]),
            ['status' => 'submitted']
            ));

            if ($request->has('package_ids')) {
                $beneficiary->packages()->sync($request->package_ids);
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'resubmit',
                'description' => 'Resubmitted beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name,
                'subject_type' => Beneficiary::class ,
                'subject_id' => $beneficiary->id,
                'ip_address' => $request->ip(),
            ]);

            // Notify managers
            $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
            $recipients = User::whereIn('role', ['manager', 'organization_admin', 'super_admin'])
                ->where('id', '!=', $request->user()->id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            Notification::sendToMany(
                $recipients,
                'beneficiary_submitted',
                'Beneficiary Resubmitted',
                $fullName . ' has been corrected and resubmitted by ' . $request->user()->name . '.',
            ['subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $beneficiary->fresh(['project', 'packages']),
                'message' => 'Beneficiary resubmitted successfully',
            ]);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to resubmit: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/v1/upload — Upload document for a beneficiary
     */
    public function upload(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'type' => 'required|string|in:id_proof,income_proof,photo,consent_form,other',
        ]);

        $beneficiary = Beneficiary::findOrFail($request->beneficiary_id);

        // Ensure the uploader is the submitter or has elevated role
        if ($request->user()->id !== $beneficiary->submitted_by &&
        !in_array($request->user()->role, ['manager', 'organization_admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $path = $request->file('file')->store('beneficiary_docs', 'public');

        $doc = BeneficiaryDocument::create([
            'beneficiary_id' => $beneficiary->id,
            'type' => $request->type,
            'file_path' => $path,
            'uploaded_by' => $request->user()->id,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'upload',
            'description' => 'Uploaded ' . $request->type . ' for beneficiary #' . $beneficiary->id,
            'subject_type' => Beneficiary::class ,
            'subject_id' => $beneficiary->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $doc,
            'message' => 'Document uploaded successfully',
        ], 201);
    }

    /**
     * POST /api/v1/beneficiary/{id}/review — Manager review (approve/reject/fraud)
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,fraud',
            'remarks' => 'required_if:action,reject,fraud|string|nullable',
            'approved_package_ids' => 'nullable|array',
            'approved_package_ids.*' => 'exists:packages,id',
        ]);

        $beneficiary = Beneficiary::findOrFail($id);

        if (!in_array($request->user()->role, ['manager', 'organization_admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $statusMap = ['approve' => 'approved', 'reject' => 'rejected', 'fraud' => 'fraud'];

        DB::beginTransaction();
        try {
            \App\Models\Review::create([
                'beneficiary_id' => $beneficiary->id,
                'manager_id' => $request->user()->id,
                'action' => $request->action,
                'remarks' => $request->remarks,
            ]);

            $beneficiary->update(['status' => $statusMap[$request->action]]);

            // Create Pending Transactions only for APPROVED packages if the action is 'approve'
            if ($request->action === 'approve') {
                $packagesToCreate = [];

                if ($request->has('approved_package_ids') && !empty($request->approved_package_ids)) {
                    // Only approve specific packages selected by the manager
                    $packagesToCreate = \App\Models\Package::whereIn('id', $request->approved_package_ids)->get();
                }
                else {
                    // Fallback: If no specific IDs provided, approve all currently assigned packages
                    $beneficiary->load('packages');
                    $packagesToCreate = $beneficiary->packages;
                }

                foreach ($packagesToCreate as $package) {
                    \App\Models\Transaction::create([
                        'beneficiary_id' => $beneficiary->id,
                        'project_id' => $beneficiary->assigned_project_id,
                        'package_id' => $package->id,
                        'financial_officer_id' => $request->user()->id,
                        'amount' => $package->value,
                        'status' => 'pending',
                    ]);
                }
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $request->action,
                'description' => ucfirst($request->action) . ' beneficiary #' . $beneficiary->id .
                ($request->has('approved_package_ids') ? ' with specific packages approved' : ''),
                'subject_type' => Beneficiary::class ,
                'subject_id' => $beneficiary->id,
                'ip_address' => $request->ip(),
            ]);

            // Notify submitter
            $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
            $typeMap = [
                'approve' => 'beneficiary_approved',
                'reject' => 'beneficiary_rejected',
                'fraud' => 'fraud_flagged',
            ];

            if ($beneficiary->submitted_by && $beneficiary->submitted_by !== $request->user()->id) {
                Notification::send(
                    $beneficiary->submitted_by,
                    $typeMap[$request->action],
                    ucfirst($statusMap[$request->action]) . ' - ' . $fullName,
                    $fullName . ' has been ' . $statusMap[$request->action] . ' by ' . $request->user()->name . '.' .
                    ($request->remarks ? ' Remarks: ' . $request->remarks : ''),
                ['subject_type' => Beneficiary::class , 'subject_id' => $beneficiary->id]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Beneficiary ' . $statusMap[$request->action] . ' successfully.',
            ]);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
