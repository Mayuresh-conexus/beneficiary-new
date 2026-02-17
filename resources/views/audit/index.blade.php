@extends('layouts.app')
@section('title', 'Audit Logs')
@section('header', 'Audit Trail')
@section('subheader', 'System-wide activity log for compliance and monitoring')

@section('content')
<!-- Filters -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('audit.index') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Action Type</label>
            <select name="action" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">All Actions</option>
                @foreach(['approve','reject','fraud','create','update','delete','login'] as $a)
                    <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
        </div>
        <button type="submit" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">Filter</button>
        <a href="{{ route('audit.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Clear</a>
    </form>
</div>

<!-- Log Entries -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Timestamp</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                @php $actionColors = ['approve'=>'bg-emerald-100 text-emerald-700','reject'=>'bg-amber-100 text-amber-700','fraud'=>'bg-rose-100 text-rose-700','create'=>'bg-sky-100 text-sky-700','update'=>'bg-violet-100 text-violet-700','delete'=>'bg-red-100 text-red-700','login'=>'bg-slate-100 text-slate-600']; @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <p class="font-medium">{{ $log->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-slate-400">{{ $log->created_at->format('h:i:s A') }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $log->user->name ?? 'System' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $actionColors[$log->action] ?? 'bg-slate-100 text-slate-600' }}">{{ $log->action }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($log->description, 60) }}</td>
                    <td class="px-6 py-4 text-xs text-slate-400 font-mono">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">No audit log entries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $logs->appends(request()->query())->links() }}</div>
</div>
@endsection
