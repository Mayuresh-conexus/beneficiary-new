<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Program;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('program.organization')->latest()->paginate(15);
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $programs = Program::with('organization')->where('is_active', true)->get();
        return view('packages.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:financial,food,medical,education,other',
            'value' => 'required|numeric|min:0',
            'frequency' => 'required|string',
        ]);

        Package::create($request->only(['program_id', 'name', 'type', 'value', 'frequency', 'conditions']));

        return redirect()->route('packages.index')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $programs = Program::with('organization')->where('is_active', true)->get();
        return view('packages.edit', compact('package', 'programs'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:financial,food,medical,education,other',
            'value' => 'required|numeric|min:0',
            'frequency' => 'required|string',
        ]);

        $package->update($request->only(['program_id', 'name', 'type', 'value', 'frequency', 'conditions']));

        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted.');
    }
}
