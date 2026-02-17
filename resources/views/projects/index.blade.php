@extends('layouts.app')
@section('title', 'Projects')
@section('header', 'Projects')
@section('subheader', 'Manage all field projects')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
    <a href="{{ route('projects.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
        <span class="material-icons text-sm">add</span> New Project
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Program</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Beneficiaries</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($projects as $project)
                @php
                    $statusColors = ['planning'=>'bg-slate-100 text-slate-600','active'=>'bg-emerald-100 text-emerald-700','completed'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-red-100 text-red-700'];
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                                <span class="material-icons text-indigo-500 text-lg">assignment</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $project->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $project->start_date ? $project->start_date->format('M Y') : '—' }} — {{ $project->end_date ? $project->end_date->format('M Y') : 'Ongoing' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $project->organization->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $project->program->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $project->location ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $project->beneficiaries_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $statusColors[$project->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $project->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('projects.show', $project) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <span class="material-icons text-slate-400 text-sm">visibility</span>
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <span class="material-icons text-slate-400 text-sm">edit</span>
                            </a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-icons text-red-400 text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">No projects found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $projects->links() }}</div>
</div>
@endsection
