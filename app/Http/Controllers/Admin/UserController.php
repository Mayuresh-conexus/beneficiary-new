<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('view_users')) {
            abort(403);
        }
        $users = User::with(['organization', 'roles'])
            ->when(!auth()->user()->isSuperAdmin(), function ($query) {
            return $query->where('organization_id', auth()->user()->organization_id);
        })
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermissionTo('create_users')) {
            abort(403);
        }
        $organizations = \App\Models\Organization::all();
        $roles = \App\Models\Role::all();

        return view('users.create', compact('organizations', 'roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create_users')) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'organization_id' => 'nullable|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        $organizationId = $request->organization_id;
        if (!auth()->user()->isSuperAdmin()) {
            $organizationId = auth()->user()->organization_id;
            if (in_array('super_admin', $request->roles)) {
                return back()->with('error', 'Unauthorized to create super admin.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->roles[0], // Keep legacy role column for now
            'organization_id' => $organizationId,
            'is_active' => $request->is_active ?? true,
        ]);

        $user->roles()->sync(\App\Models\Role::whereIn('name', $request->roles)->pluck('id'));

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasPermissionTo('edit_users')) {
            abort(403);
        }
        // Scoping
        if (!auth()->user()->isSuperAdmin() && $user->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $organizations = \App\Models\Organization::all();
        $roles = \App\Models\Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'organizations', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('edit_users')) {
            abort(403);
        }
        // Scoping
        if (!auth()->user()->isSuperAdmin() && $user->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'organization_id' => 'nullable|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'email', 'is_active']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if (auth()->user()->isSuperAdmin()) {
            $data['organization_id'] = $request->organization_id;
        }
        else {
            unset($data['organization_id']);
            if (in_array('super_admin', $request->roles)) {
                return back()->with('error', 'Unauthorized to assign super admin role.');
            }
        }

        $data['role'] = $request->roles[0]; // Legacy fallback
        $user->update($data);
        $user->roles()->sync(\App\Models\Role::whereIn('name', $request->roles)->pluck('id'));

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->hasPermissionTo('delete_users')) {
            abort(403);
        }
        // Scoping
        if (!auth()->user()->isSuperAdmin() && $user->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
