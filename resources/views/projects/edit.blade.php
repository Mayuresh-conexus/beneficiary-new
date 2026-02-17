@extends('layouts.app')
@section('title', 'Edit Project')
@section('header', 'Edit Project')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Organization *</label>
                    <select name="organization_id" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ $project->organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Program *</label>
                    <select name="program_id" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" {{ $project->program_id == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Project Name *</label>
                    <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">{{ old('description', $project->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Location</label>
                    <input type="text" name="location" value="{{ old('location', $project->location) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        @foreach(['planning','active','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Packages</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($packages as $pkg)
                        <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="package_ids[]" value="{{ $pkg->id }}" {{ $project->packages->contains($pkg->id) ? 'checked' : '' }} class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                            <span class="text-sm font-medium text-slate-700">{{ $pkg->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Assigned Users</label>
                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3">
                        @foreach($users as $user)
                        <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" {{ $project->assignedUsers->contains($user->id) ? 'checked' : '' }} class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                            <span class="text-sm font-medium text-slate-700">{{ $user->name }} <span class="text-[10px] uppercase font-bold text-slate-400">({{ $user->role }})</span></span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Update Project</button>
                <a href="{{ route('projects.index') }}" class="px-6 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
