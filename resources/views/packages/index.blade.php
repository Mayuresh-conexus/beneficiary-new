@extends('layouts.app')
@section('title', 'Packages')
@section('header', 'Packages')
@section('subheader', 'Manage aid packages across programs')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
    <a href="{{ route('packages.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
        <span class="material-icons text-sm">add</span> New Package
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Package</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Program</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Frequency</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($packages as $package)
                @php
                    $typeColors = ['financial'=>'bg-emerald-100 text-emerald-700','food'=>'bg-orange-100 text-orange-700','medical'=>'bg-rose-100 text-rose-700','education'=>'bg-sky-100 text-sky-700','other'=>'bg-slate-100 text-slate-600'];
                    $typeIcons = ['financial'=>'payments','food'=>'restaurant','medical'=>'local_hospital','education'=>'school','other'=>'category'];
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $typeColors[$package->type] ?? 'bg-slate-100' }} flex items-center justify-center">
                                <span class="material-icons text-lg">{{ $typeIcons[$package->type] ?? 'category' }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-800">{{ $package->name }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $package->program->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $typeColors[$package->type] ?? 'bg-slate-100 text-slate-600' }}">{{ $package->type }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-700">${{ number_format($package->value, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $package->frequency }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('packages.edit', $package) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <span class="material-icons text-slate-400 text-sm">edit</span>
                            </a>
                            <form method="POST" action="{{ route('packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-icons text-red-400 text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">No packages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $packages->links() }}</div>
</div>
@endsection
