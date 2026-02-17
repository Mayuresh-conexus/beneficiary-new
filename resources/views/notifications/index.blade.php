@extends('layouts.app')
@section('title', 'Notifications')
@section('header', 'Notifications')
@section('subheader', 'Stay updated on all system activity')

@section('actions')
<form method="POST" action="{{ route('notifications.markAllRead') }}">
    @csrf
    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
        <span class="material-icons text-sm">done_all</span>
        Mark All as Read
    </button>
</form>
@endsection

@section('content')
<div class="space-y-3">
    @forelse($notifications as $notification)
    <div class="bg-white rounded-xl border {{ $notification->isRead() ? 'border-slate-100' : 'border-primary/20 bg-primary/[0.02]' }} shadow-sm hover:shadow-md transition-all p-5 flex items-start gap-4">
        @php
            $colorMap = [
                'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-500'],
                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500'],
                'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-500'],
                'rose'    => ['bg' => 'bg-rose-50',     'text' => 'text-rose-500'],
                'red'     => ['bg' => 'bg-red-50',      'text' => 'text-red-500'],
                'violet'  => ['bg' => 'bg-violet-50',   'text' => 'text-violet-500'],
                'primary' => ['bg' => 'bg-primary/10',  'text' => 'text-primary'],
            ];
            $colors = $colorMap[$notification->color] ?? $colorMap['primary'];
        @endphp

        <div class="w-11 h-11 rounded-full {{ $colors['bg'] }} flex items-center justify-center shrink-0">
            <span class="material-icons {{ $colors['text'] }} text-lg">{{ $notification->icon }}</span>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold {{ $notification->isRead() ? 'text-slate-500' : 'text-slate-800' }}">
                        {{ $notification->title }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">{{ $notification->message }}</p>
                </div>
                @if(!$notification->isRead())
                <span class="w-2.5 h-2.5 bg-primary rounded-full shrink-0 mt-1.5"></span>
                @endif
            </div>
            <div class="flex items-center gap-4 mt-3">
                <span class="text-[11px] text-slate-400">
                    <span class="material-icons text-[11px] align-middle mr-0.5">schedule</span>
                    {{ $notification->created_at->diffForHumans() }}
                </span>

                @if(!$notification->isRead())
                <form method="POST" action="{{ route('notifications.read', $notification) }}" class="inline">
                    @csrf
                    <button type="submit" class="text-[11px] text-primary font-semibold hover:underline">Mark as read</button>
                </form>
                @endif

                @if($notification->link)
                <a href="{{ $notification->link }}" class="text-[11px] text-primary font-semibold hover:underline flex items-center gap-0.5">
                    View <span class="material-icons text-[11px]">arrow_forward</span>
                </a>
                @endif

                <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline ml-auto"
                      onsubmit="return confirm('Delete this notification?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-[11px] text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-icons text-sm">delete_outline</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-icons text-slate-300 text-3xl">notifications_off</span>
        </div>
        <h3 class="text-lg font-bold text-slate-600">No notifications</h3>
        <p class="text-sm text-slate-400 mt-1">You're all caught up! Check back later for updates.</p>
    </div>
    @endforelse

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
