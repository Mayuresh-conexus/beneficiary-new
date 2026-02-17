@extends('layouts.app')
@section('title', 'Create User')
@section('header', 'Create User')
@section('subheader', 'Add a new user and assign roles')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-8">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="John Doe"/>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="john@example.com"/>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password *</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="Min. 8 characters"/>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Organization</label>
                    <select name="organization_id" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="">System (No Organization)</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                    @error('organization_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-4">Roles *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 hover:border-primary/20 hover:bg-primary/[0.02] cursor-pointer transition-all">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ (is_array(old('roles')) && in_array($role->name, old('roles'))) ? 'checked' : '' }} class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ $role->display_name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">{{ $role->name }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} id="is_active" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                    <label for="is_active" class="text-sm font-semibold text-slate-700">Account is Active</label>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Create User</button>
                <a href="{{ route('users.index') }}" class="px-6 py-3 rounded-lg text-sm font-semibold border border-slate-200 hover:bg-slate-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
