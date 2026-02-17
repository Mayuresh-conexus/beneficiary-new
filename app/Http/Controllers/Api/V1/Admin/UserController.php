<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * GET /api/v1/admin/users — List all users
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $query = User::with('organization');

        // Scoping for non-super admins
        if (!$request->user()->isSuperAdmin()) {
            $query->where('organization_id', $request->user()->organization_id);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * POST /api/v1/admin/users — Create a new user
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin($request);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(User::getRoles())],
            'organization_id' => 'nullable|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        // Ensure organization matching for non-super admins
        $organizationId = $request->organization_id;
        if (!$request->user()->isSuperAdmin()) {
            $organizationId = $request->user()->organization_id;

            // Prevent organization_admin from creating super_admin
            if ($request->role === User::ROLE_SUPER_ADMIN) {
                abort(403, 'Unauthorized. Cannot create super admin.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'organization_id' => $organizationId,
            'is_active' => $request->is_active ?? true,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_user',
            'description' => "Created user {$user->name} with role {$user->role}",
            'subject_type' => User::class ,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    /**
     * GET /api/v1/admin/users/{id} — Show user details
     */
    public function show(Request $request, $id)
    {
        $this->authorizeAdmin($request);

        $user = User::with(['organization', 'assignedProjects'])->findOrFail($id);

        // Scoping
        if (!$request->user()->isSuperAdmin() && $user->organization_id !== $request->user()->organization_id) {
            abort(403, 'Unauthorized. This user belongs to a different organization.');
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * PUT /api/v1/admin/users/{id} — Update user
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin($request);

        $user = User::findOrFail($id);

        // Scoping
        if (!$request->user()->isSuperAdmin() && $user->organization_id !== $request->user()->organization_id) {
            abort(403, 'Unauthorized. This user belongs to a different organization.');
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'role' => ['sometimes', Rule::in(User::getRoles())],
            'organization_id' => 'nullable|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $data = $request->except(['password']);

        // Restrictions for organization admins
        if (!$request->user()->isSuperAdmin()) {
            unset($data['organization_id']); // Cannot change organization
            if (isset($data['role']) && $data['role'] === User::ROLE_SUPER_ADMIN) {
                abort(403, 'Unauthorized. Cannot assign super admin role.');
            }
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_user',
            'description' => "Updated user {$user->name}",
            'subject_type' => User::class ,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * DELETE /api/v1/admin/users/{id}
     */
    public function destroy(Request $request, $id)
    {
        $this->authorizeAdmin($request);

        $user = User::findOrFail($id);

        // Scoping
        if (!$request->user()->isSuperAdmin() && $user->organization_id !== $request->user()->organization_id) {
            abort(403, 'Unauthorized. This user belongs to a different organization.');
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Cannot delete yourself'], 400);
        }

        $user->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_user',
            'description' => "Soft deleted user {$user->name}",
            'subject_type' => User::class ,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Access Control check
     */
    private function authorizeAdmin(Request $request)
    {
        if (!$request->user()->isSuperAdmin() && $request->user()->role !== User::ROLE_ORGANIZATION_ADMIN) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }
}
