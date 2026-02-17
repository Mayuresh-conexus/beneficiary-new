@extends('layouts.app')
@section('title', $project->name)
@section('header', $project->name)
@section('subheader', $project->location ?? 'No location set')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
        <p class="text-xs font-bold uppercase text-slate-400 mb-2">Organization</p>
        <p class="text-lg font-bold text-slate-800">{{ $project->organization->name ?? '—' }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
        <p class="text-xs font-bold uppercase text-slate-400 mb-2">Program</p>
        <p class="text-lg font-bold text-slate-800">{{ $project->program->name ?? '—' }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
        <p class="text-xs font-bold uppercase text-slate-400 mb-2">Status / Duration</p>
        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-emerald-100 text-emerald-700">{{ $project->status }}</span>
        <p class="text-xs text-slate-400 mt-2">{{ $project->start_date?->format('M d, Y') }} — {{ $project->end_date?->format('M d, Y') ?? 'Ongoing' }}</p>
    </div>
</div>

<!-- Packages -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-slate-800 mb-4">Assigned Packages</h3>
    <div class="flex flex-wrap gap-2">
        @forelse($project->packages as $pkg)
        <span class="px-3 py-1.5 text-xs font-semibold bg-primary/10 text-primary rounded-lg">{{ $pkg->name }} (${{ $pkg->value }})</span>
        @empty
        <p class="text-sm text-slate-400">No packages assigned.</p>
        @endforelse
    </div>
</div>

<!-- Assigned Users -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-slate-800 mb-4">Assigned Team</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @forelse($project->assignedUsers as $user)
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="material-icons text-primary text-sm">person</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
                <p class="text-[10px] uppercase font-bold text-slate-400">{{ str_replace('_', ' ', $user->role) }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-slate-400">No users assigned.</p>
        @endforelse
    </div>
</div>

<!-- Beneficiaries in Project -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Beneficiaries ({{ $project->beneficiaries->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($project->beneficiaries as $b)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-sm font-semibold text-slate-800">{{ $b->first_name }} {{ $b->last_name }}</td>
                    <td class="px-6 py-3 text-sm text-slate-500">{{ $b->government_id }}</td>
                    <td class="px-6 py-3">
                        @php $sc = ['approved'=>'bg-emerald-100 text-emerald-700','fraud'=>'bg-rose-100 text-rose-700','submitted'=>'bg-amber-100 text-amber-700','under_review'=>'bg-sky-100 text-sky-700','rejected'=>'bg-red-100 text-red-700','draft'=>'bg-slate-100 text-slate-500']; @endphp
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $sc[$b->status] ?? 'bg-slate-100 text-slate-500' }}">{{ str_replace('_', ' ', $b->status) }}</span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('beneficiaries.show', $b) }}" class="text-primary text-sm font-semibold hover:underline">Review</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-slate-400">No beneficiaries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
