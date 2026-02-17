@extends('layouts.app')
@section('title', 'Edit Organization')
@section('header', 'Edit Organization')
@section('subheader', 'Update organization details')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('organizations.update', $organization) }}">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Organization Name *</label>
                    <input type="text" name="name" value="{{ old('name', $organization->name) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">{{ old('address', $organization->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $organization->contact_number) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="active" {{ $organization->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $organization->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Update Organization</button>
                <a href="{{ route('organizations.index') }}" class="px-6 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
