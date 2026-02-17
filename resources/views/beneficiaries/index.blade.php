@extends('layouts.app')
@section('title', 'Beneficiaries')
@section('header', 'Beneficiary Directory')
@section('subheader', 'View and manage all submitted beneficiaries')

@section('actions')
<a href="{{ route('beneficiaries.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-primary/25 hover:bg-primary/90 transition-all flex items-center gap-2">
    <span class="material-icons text-sm">person_add</span> Register Beneficiary
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('beneficiaries.index') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Gov ID..." class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
            <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">All Statuses</option>
                @foreach(['draft','submitted','under_review','approved','rejected','fraud'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">
            <span class="material-icons text-sm align-middle">search</span> Filter
        </button>
        <a href="{{ route('beneficiaries.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Clear</a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Beneficiary</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Gov ID</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Project / Org</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Submitted By</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $sc = ['approved'=>'bg-emerald-100 text-emerald-700','fraud'=>'bg-rose-100 text-rose-700','submitted'=>'bg-amber-100 text-amber-700','under_review'=>'bg-sky-100 text-sky-700','rejected'=>'bg-red-100 text-red-700','draft'=>'bg-slate-100 text-slate-500']; @endphp
                @forelse($beneficiaries as $b)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                                <span class="material-icons text-slate-400 text-lg">person</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $b->first_name }} {{ $b->last_name }}</p>
                                <p class="text-[11px] text-slate-400">Ref: #BEN-{{ $b->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 font-mono">{{ $b->government_id }}</td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-600">{{ $b->project->name ?? '—' }}</p>
                        <p class="text-[11px] text-slate-400">{{ $b->project->organization->name ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $b->submitter->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $b->created_at?->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $sc[$b->status] ?? 'bg-slate-100' }}">{{ str_replace('_', ' ', $b->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('beneficiaries.show', $b) }}" class="bg-primary/10 text-primary px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-primary/20 transition-colors">Review</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">No beneficiaries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $beneficiaries->appends(request()->query())->links() }}</div>
</div>
@endsection
