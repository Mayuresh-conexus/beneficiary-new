@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('header', 'Roles & Permissions')
@section('subheader', 'Manage user roles and their associated permissions')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
@if(auth()->user()->hasPermissionTo('manage_roles'))
    <a href="{{ route('roles.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
        <span class="material-icons text-sm">security</span> New Role
    </a>
@endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($roles as $role)
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-icons text-primary">admin_panel_settings</span>
                </div>
                <div class="flex gap-1">
                    @if(auth()->user()->hasPermissionTo('manage_roles'))
                    <a href="{{ route('roles.edit', $role) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                        <span class="material-icons text-slate-400 text-sm">edit</span>
                    </a>
                    @if($role->name !== 'super_admin')
                    <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Are you sure?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors">
                            <span class="material-icons text-red-400 text-sm">delete</span>
                        </button>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
            <h3 class="text-lg font-bold text-slate-800">{{ $role->display_name }}</h3>
            <p class="text-xs text-slate-400 font-mono mt-1">{{ $role->name }}</p>
            
            <div class="mt-6 flex items-center gap-2">
                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-slate-100 text-slate-600">
                    {{ $role->permissions_count }} Permissions
                </span>
            </div>
        </div>
        @if(auth()->user()->hasPermissionTo('manage_roles'))
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            <a href="{{ route('roles.edit', $role) }}" class="text-sm font-semibold text-primary hover:underline flex items-center gap-2">
                Manage Permissions <span class="material-icons text-xs">arrow_forward</span>
            </a>
        </div>
        @endif
    </div>
    @empty
    <div class="col-span-full py-12 text-center text-slate-400">No roles found.</div>
    @endforelse
</div>
@endsection
