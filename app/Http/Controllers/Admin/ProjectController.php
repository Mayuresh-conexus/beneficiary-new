<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Organization;
use App\Models\Program;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['organization', 'program'])->withCount('beneficiaries')->latest()->paginate(15);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $organizations = Organization::where('status', 'active')->get();
        $programs = Program::where('is_active', true)->get();
        $packages = Package::all();
        $users = User::whereIn('role', ['manager', 'volunteer'])->where('is_active', true)->get();
        return view('projects.create', compact('organizations', 'programs', 'packages', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,completed,cancelled',
        ]);

        $project = Project::create($request->only([
            'organization_id', 'program_id', 'name', 'description',
            'location', 'start_date', 'end_date', 'status'
        ]));

        if ($request->has('package_ids')) {
            $project->packages()->attach($request->package_ids);
        }

        if ($request->has('user_ids')) {
            $project->assignedUsers()->attach($request->user_ids);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['organization', 'program', 'packages', 'assignedUsers', 'beneficiaries']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $organizations = Organization::where('status', 'active')->get();
        $programs = Program::where('is_active', true)->get();
        $packages = Package::all();
        $users = User::whereIn('role', ['manager', 'volunteer'])->where('is_active', true)->get();
        $project->load(['packages', 'assignedUsers']);
        return view('projects.edit', compact('project', 'organizations', 'programs', 'packages', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,completed,cancelled',
        ]);

        $project->update($request->only([
            'organization_id', 'program_id', 'name', 'description',
            'location', 'start_date', 'end_date', 'status'
        ]));

        $project->packages()->sync($request->package_ids ?? []);
        $project->assignedUsers()->sync($request->user_ids ?? []);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    // AJAX: Get programs by organization
    public function programsByOrganization($orgId)
    {
        return response()->json(Program::where('organization_id', $orgId)->where('is_active', true)->get());
    }

    // AJAX: Get packages by program
    public function packagesByProgram($programId)
    {
        return response()->json(Package::where('program_id', $programId)->get());
    }

    // AJAX: Get packages by project
    public function packagesByProject($projectId)
    {
        $project = Project::with('packages')->findOrFail($projectId);
        return response()->json($project->packages);
    }
}
