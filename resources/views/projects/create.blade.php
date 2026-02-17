@extends('layouts.app')
@section('title', 'Create Project')
@section('header', 'Project Creation Wizard')
@section('subheader', 'Select Organization → Program → Packages')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <div class="space-y-6">
                <!-- Step 1: Organization -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <span class="inline-flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-primary text-white text-xs flex items-center justify-center font-bold">1</span> Organization *</span>
                    </label>
                    <select name="organization_id" id="org_select" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Step 2: Program (dependent) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <span class="inline-flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-primary text-white text-xs flex items-center justify-center font-bold">2</span> Program *</span>
                    </label>
                    <select name="program_id" id="program_select" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="">Select Organization first</option>
                    </select>
                </div>

                <!-- Step 3: Packages (dependent) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <span class="inline-flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-primary text-white text-xs flex items-center justify-center font-bold">3</span> Packages</span>
                    </label>
                    <div id="packages_container" class="grid grid-cols-2 gap-2">
                        <p class="text-sm text-slate-400 col-span-2">Select a program to view available packages</p>
                    </div>
                </div>

                <hr class="border-slate-200"/>

                <!-- Project Details -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Project Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="e.g. Nairobi East Distribution"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="planning">Planning</option>
                        <option value="active" selected>Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Assign Users -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Assign Managers & Volunteers</label>
                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3">
                        @foreach($users as $user)
                        <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                            <div>
                                <span class="text-sm font-medium text-slate-700">{{ $user->name }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $user->role === 'manager' ? 'bg-primary/10 text-primary' : 'bg-sky-100 text-sky-700' }} ml-1 uppercase font-bold">{{ $user->role }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Create Project</button>
                <a href="{{ route('projects.index') }}" class="px-6 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('org_select').addEventListener('change', function() {
        const orgId = this.value;
        const programSelect = document.getElementById('program_select');
        programSelect.innerHTML = '<option value="">Loading...</option>';
        document.getElementById('packages_container').innerHTML = '<p class="text-sm text-slate-400 col-span-2">Select a program...</p>';

        if (!orgId) { programSelect.innerHTML = '<option value="">Select Organization first</option>'; return; }

        fetch('/ajax/programs-by-org/' + orgId)
            .then(r => r.json())
            .then(programs => {
                programSelect.innerHTML = '<option value="">Select Program</option>';
                programs.forEach(p => {
                    programSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            });
    });

    document.getElementById('program_select').addEventListener('change', function() {
        const programId = this.value;
        const container = document.getElementById('packages_container');
        container.innerHTML = '<p class="text-sm text-slate-400 col-span-2">Loading...</p>';

        if (!programId) { container.innerHTML = '<p class="text-sm text-slate-400 col-span-2">Select a program...</p>'; return; }

        fetch('/ajax/packages-by-program/' + programId)
            .then(r => r.json())
            .then(packages => {
                if (packages.length === 0) {
                    container.innerHTML = '<p class="text-sm text-slate-400 col-span-2">No packages for this program.</p>';
                    return;
                }
                container.innerHTML = '';
                packages.forEach(pkg => {
                    container.innerHTML += `
                        <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="package_ids[]" value="${pkg.id}" class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                            <div>
                                <span class="text-sm font-medium text-slate-700">${pkg.name}</span>
                                <span class="text-xs text-slate-400 block">$${parseFloat(pkg.value).toFixed(2)} • ${pkg.frequency}</span>
                            </div>
                        </label>`;
                });
            });
    });
</script>
@endpush
@endsection
