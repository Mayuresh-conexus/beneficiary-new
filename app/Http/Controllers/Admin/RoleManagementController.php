<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('view_roles')) {
            abort(403);
        }
        $roles = Role::withCount('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403);
        }
        $permissionsByGroup = Permission::all()->groupBy('group');
        return view('roles.create', compact('permissionsByGroup'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|unique:roles,name|string|max:255',
            'display_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => str_replace(' ', '_', strtolower($request->name)),
            'display_name' => $request->display_name,
        ]);

        if ($request->has('permissions')) {
            $permissionIds = Permission::whereIn('name', $request->permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403);
        }
        $permissionsByGroup = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissionsByGroup', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403);
        }
        $request->validate([
            'display_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update([
            'display_name' => $request->display_name,
        ]);

        if ($request->has('permissions')) {
            $permissionIds = Permission::whereIn('name', $request->permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
        else {
            $role->permissions()->detach();
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403);
        }
        if (in_array($role->name, ['super_admin'])) {
            return back()->with('error', 'The super_admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
