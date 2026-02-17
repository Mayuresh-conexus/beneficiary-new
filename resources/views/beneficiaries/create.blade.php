@extends('layouts.app')
@section('title', 'Register Beneficiary')
@section('header', 'Register New Beneficiary')
@section('subheader', 'Add a new beneficiary record to the system')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('beneficiaries.store') }}" class="space-y-8">
            @csrf
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-icons text-primary text-xl">person</span> Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="John"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="Doe"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Government ID / Passport *</label>
                        <input type="text" name="government_id" value="{{ old('government_id') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="ID Number"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gender</label>
                        <select name="gender" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Contact Number</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="+1..."/>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-icons text-primary text-xl">location_on</span> Address & Location
                </h3>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Physical Address</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="Street name, Building, Area...">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Program Assignment -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-icons text-primary text-xl">assignment</span> Project & Packages
                </h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Assigned Project *</label>
                        <select name="assigned_project_id" id="projectSelect" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                            <option value="">Select a Project</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ old('assigned_project_id') == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->organization->name }} — {{ $proj->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="packageSection" class="hidden">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Select Packages</label>
                        <div id="packageList" class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 border border-slate-100 bg-slate-50 rounded-xl">
                            <!-- Populated via AJAX -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex gap-3">
                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/25 hover:bg-primary/90 transition-all flex items-center gap-2">
                    <span class="material-icons text-sm">how_to_reg</span> Register Beneficiary
                </button>
                <a href="{{ route('beneficiaries.index') }}" class="px-8 py-3 rounded-xl font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('projectSelect').addEventListener('change', function() {
    const projectId = this.value;
    const packageSection = document.getElementById('packageSection');
    const packageList = document.getElementById('packageList');

    if (!projectId) {
        packageSection.classList.add('hidden');
        return;
    }

    // Fetch packages for this project's program
    // We can use the existing AJAX helper we built for the project wizard
    // Actually, Project has packages. Let's use that.
    fetch(`/ajax/packages-by-project/${projectId}`) 
        .then(res => res.json())
        .then(data => {
            packageList.innerHTML = '';
            if (data.length > 0) {
                packageSection.classList.remove('hidden');
                data.forEach(pkg => {
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-primary transition-colors';
                    label.innerHTML = `
                        <input type="checkbox" name="package_ids[]" value="${pkg.id}" class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary"/>
                        <div>
                            <p class="text-sm font-bold text-slate-700">${pkg.name}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">${pkg.type} • $${pkg.value}</p>
                        </div>
                    `;
                    packageList.appendChild(label);
                });
            } else {
                packageSection.classList.add('hidden');
            }
        });
});
</script>
@endpush
@endsection
