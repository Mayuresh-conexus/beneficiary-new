<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * GET /api/v1/transactions — List transactions
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Volunteers see transactions for projects they are assigned to
        $projectIds = $user->assignedProjects()->pluck('projects.id');

        $query = Transaction::whereIn('project_id', $projectIds)
            ->with(['beneficiary', 'package', 'project']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('beneficiary', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('government_id', 'like', "%$search%")
                    ->orWhere('uuid', 'like', "%$search%"); // Transaction UUID
            });
        }

        $transactions = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * POST /api/v1/transactions/{id}/deliver — Mark as delivered
     */
    public function deliver(Request $request, $id)
    {
        $request->validate([
            'signature_image' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'notes' => 'nullable|string',
            'biometric_verified' => 'nullable|boolean',
            'biometric_device' => 'nullable|string',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'delivered') {
            return response()->json(['success' => false, 'message' => 'Already delivered'], 400);
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'status' => 'delivered',
                'delivered_by' => $request->user()->id,
                'delivery_date' => now(),
                'remarks' => $request->notes,
                'biometric_verified' => $request->boolean('biometric_verified'),
                'biometric_verified_at' => $request->boolean('biometric_verified') ? now() : null,
                'biometric_device' => $request->biometric_device,
            ];

            if ($request->hasFile('signature_image')) {
                $path = $request->file('signature_image')->store('signatures', 'public');
                $updateData['signature_path'] = $path;
            }

            $transaction->update($updateData);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'deliver',
                'description' => 'Delivered package ' . ($transaction->package->name ?? 'Unknown') .
                ' to ' . ($transaction->beneficiary->first_name ?? 'Unknown') .
                ($request->boolean('biometric_verified') ? ' (Biometrically Verified)' : ''),
                'subject_type' => Transaction::class ,
                'subject_id' => $transaction->id,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery recorded successfully' . ($request->boolean('biometric_verified') ? ' with biometric confirmation' : ''),
                'data' => $transaction->fresh(),
            ]);

        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
