@extends('layouts.app')
@section('title', 'Create Role')
@section('header', 'Create Role')
@section('subheader', 'Define a new role and select its permissions')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Role Name (Internal) *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="e.g. project_manager"/>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Display Name *</label>
                        <input type="text" name="display_name" value="{{ old('display_name') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="e.g. Project Manager"/>
                        @error('display_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <h4 class="font-bold text-slate-800 mb-6">Assign Permissions</h4>
                    
                    @foreach($permissionsByGroup as $group => $permissions)
                    <div class="mb-8">
                        <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">{{ $group }}</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($permissions as $permission)
                            <label class="group flex items-start gap-3 p-4 rounded-xl border border-slate-100 hover:border-primary/20 hover:bg-primary/[0.02] cursor-pointer transition-all">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="mt-1 w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"/>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 group-hover:text-primary transition-colors">{{ $permission->display_name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">{{ $permission->name }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex gap-3 pt-6 border-t border-slate-100">
                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Create Role</button>
                <a href="{{ route('roles.index') }}" class="px-8 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
