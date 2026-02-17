@extends('layouts.app')
@section('title', 'Edit Package')
@section('header', 'Edit Package')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('packages.update', $package) }}">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Program *</label>
                    <select name="program_id" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" {{ $package->program_id == $prog->id ? 'selected' : '' }}>{{ $prog->name }} ({{ $prog->organization->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Package Name *</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Type *</label>
                        <select name="type" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                            @foreach(['financial','food','medical','education','other'] as $type)
                                <option value="{{ $type }}" {{ $package->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Value ($) *</label>
                        <input type="number" name="value" value="{{ old('value', $package->value) }}" step="0.01" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Frequency *</label>
                    <select name="frequency" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        @foreach(['One-time','Weekly','Monthly','Quarterly','Yearly'] as $freq)
                            <option value="{{ $freq }}" {{ $package->frequency === $freq ? 'selected' : '' }}>{{ $freq }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Update Package</button>
                <a href="{{ route('packages.index') }}" class="px-6 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
