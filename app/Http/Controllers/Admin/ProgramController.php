<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Organization;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('organization')->withCount('packages')->latest()->paginate(15);
        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        $organizations = Organization::where('status', 'active')->get();
        return view('programs.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Program::create($request->only(['organization_id', 'name', 'description', 'is_active']));

        return redirect()->route('programs.index')->with('success', 'Program created successfully.');
    }

    public function edit(Program $program)
    {
        $organizations = Organization::where('status', 'active')->get();
        return view('programs.edit', compact('program', 'organizations'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $program->update($request->only(['organization_id', 'name', 'description', 'is_active']));

        return redirect()->route('programs.index')->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('programs.index')->with('success', 'Program deleted.');
    }
}
