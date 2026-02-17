<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('view_organizations')) {
            abort(403);
        }
        $organizations = Organization::withCount(['programs', 'projects', 'users'])->latest()->paginate(15);
        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermissionTo('manage_organizations')) {
            abort(403);
        }
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_organizations')) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        Organization::create([
            'name' => $request->name,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('organizations.index')->with('success', 'Organization created successfully.');
    }

    public function edit(Organization $organization)
    {
        if (!auth()->user()->hasPermissionTo('manage_organizations')) {
            abort(403);
        }
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        if (!auth()->user()->hasPermissionTo('manage_organizations')) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $organization->update($request->only(['name', 'address', 'contact_number', 'status']));

        return redirect()->route('organizations.index')->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization)
    {
        if (!auth()->user()->hasPermissionTo('manage_organizations')) {
            abort(403);
        }
        $organization->delete();
        return redirect()->route('organizations.index')->with('success', 'Organization deleted successfully.');
    }
}
