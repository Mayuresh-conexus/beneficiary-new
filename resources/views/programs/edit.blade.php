@extends('layouts.app')
@section('title', 'Edit Program')
@section('header', 'Edit Program')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('programs.update', $program) }}">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Organization *</label>
                    <select name="organization_id" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ $program->organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Program Name *</label>
                    <input type="text" name="name" value="{{ old('name', $program->name) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">{{ old('description', $program->description) }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0"/>
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ $program->is_active ? 'checked' : '' }} class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                    <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Update Program</button>
                <a href="{{ route('programs.index') }}" class="px-6 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
