@extends('layouts.app')
@section('title', 'Programs')
@section('header', 'Programs & Packages Library')
@section('subheader', 'Manage all programs across organizations')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
    <a href="{{ route('programs.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
        <span class="material-icons text-sm">add</span> New Program
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Program</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Packages</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($programs as $program)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center">
                                <span class="material-icons text-sky-500 text-lg">account_tree</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $program->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ Str::limit($program->description, 40) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $program->organization->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $program->packages_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $program->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $program->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('programs.edit', $program) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <span class="material-icons text-slate-400 text-sm">edit</span>
                            </a>
                            <form method="POST" action="{{ route('programs.destroy', $program) }}" onsubmit="return confirm('Delete this program?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-icons text-red-400 text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">No programs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $programs->links() }}</div>
</div>
@endsection
