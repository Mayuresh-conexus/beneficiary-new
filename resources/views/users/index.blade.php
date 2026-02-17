@extends('layouts.app')
@section('title', 'User Management')
@section('header', 'User Management')
@section('subheader', 'Manage all users and their roles')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
@if(auth()->user()->hasPermissionTo('create_users'))
    <a href="{{ route('users.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
        <span class="material-icons text-sm">person_add</span> New User
    </a>
@endif
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                <span class="material-icons text-primary text-lg">person</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-400">Joined {{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $user->organization->name ?? 'System' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-blue-100 text-blue-700">
                                {{ $role->display_name }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-emerald-100 text-emerald-700">Active</span>
                        @else
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-rose-100 text-rose-700">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if(auth()->user()->hasPermissionTo('edit_users'))
                            <a href="{{ route('users.edit', $user) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <span class="material-icons text-slate-400 text-sm">edit</span>
                            </a>
                            @endif
                            
                            @if(auth()->user()->hasPermissionTo('delete_users'))
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <span class="material-icons text-red-400 text-sm">delete</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
</div>
@endsection
