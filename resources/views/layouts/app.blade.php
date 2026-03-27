<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ config('app.name', 'ImpactNexus') }} - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --color-primary: 30 30 138;
            /* Default Sidebar (Modern Dark) */
            --sidebar-bg: linear-gradient(180deg, #171719 0%, #171719 50%, #252529 100%);
            --sidebar-text: #ffffff;
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --sidebar-active: rgba(255, 255, 255, 0.1);
        }
        
        /* Impact Blue Theme (Legacy) */
        [data-sidebar="blue"] {
            --sidebar-bg: rgb(30, 30, 138);
            --sidebar-text: #ffffff;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --sidebar-active: rgba(255, 255, 255, 0.2);
        }

        /* Dark Sidebar Theme */
        [data-sidebar="dark"] {
            --sidebar-bg: #1e293b; /* Slate 800 - Premium Dark */
            --sidebar-text: #e2e8f0; /* Slate 200 */
            --sidebar-hover: #334155; /* Slate 700 */
            --sidebar-active: #2563eb; /* Blue 600 */
        }
        
        /* Premium Midnight Theme */
        [data-sidebar="midnight"] {
            --sidebar-bg: #0f172a; /* Slate 900 */
            --sidebar-text: #cbd5e1; /* Slate 300 */
            --sidebar-hover: #1e293b; /* Slate 800 */
            --sidebar-active: #3b82f6; /* Blue 500 */
        }

        /* Shine Effect */
        .shining-text {
            background: linear-gradient(90deg, #94a3b8 0%, #ffffff 40%, #ffffff 50%, #ffffff 60%, #94a3b8 100%);
            background-size: 200% auto;
            color: #fff;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
            font-weight: 800;
        }
        
        [data-sidebar="blue"] .shining-text {
             background: linear-gradient(90deg, #94a3b8 0%, #cbd5e1 40%, #ffffff 50%, #cbd5e1 60%, #94a3b8 100%);
             background-size: 200% auto;
             -webkit-background-clip: text;
             -webkit-text-fill-color: transparent;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }
        
        /* Box Shine */
        .shining-box {
            position: relative;
            overflow: hidden;
        }
        .shining-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 150%;
            height: 100%;
            background: linear-gradient(
                90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.2) 50%, 
                transparent 100%
            );
            transform: skewX(-25deg);
            animation: boxShine 6s infinite;
            pointer-events: none;
        }
        
        @keyframes boxShine {
            0% { left: -150%; }
            20% { left: 150%; }
            100% { left: 150%; }
        }
    </style>
    <script>
        // Apply saved theme immediately
        const savedColor = localStorage.getItem('theme_color');
        if(savedColor) document.documentElement.style.setProperty('--color-primary', savedColor);
        
        const savedSidebar = localStorage.getItem('sidebar_theme');
        if(savedSidebar) document.documentElement.setAttribute('data-sidebar', savedSidebar);

        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "rgb(var(--color-primary))",
                        "background-light": "#f6f6f8",
                        "background-dark": "#121220",
                    },
                    fontFamily: { "display": ["Inter"] },
                    borderRadius: {"DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px"},
                },
            },
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="font-display bg-background-light text-slate-800 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 flex flex-col transition-all duration-300 ease-in-out shrink-0 sticky top-0 h-screen" 
           style="background: var(--sidebar-bg); color: var(--sidebar-text);">
        <div class="px-4 pt-6 pb-2">
            <div class="shining-box rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-3 flex flex-col items-center gap-3 group transition-all hover:bg-white/10">
                <!-- App Logo Container -->
                <div class="bg-white p-1.5 rounded-lg shadow-lg ring-1 ring-black/5">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
                </div>
                
                <!-- App Text -->
                <div class="text-center">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] shining-text leading-tight opacity-90">Beneficiary App</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 px-4 py-2 space-y-1 overflow-y-auto">
            @php $current = Route::currentRouteName(); @endphp

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('dashboard') }}"
               style="{{ $current === 'dashboard' ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ $current === 'dashboard' ? '1' : '0.7' }}">dashboard</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ $current === 'dashboard' ? '1' : '0.7' }}">Dashboard</span>
            </a>
            @if(auth()->user()->hasPermissionTo('view_organizations'))
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('organizations.index') }}"
                style="{{ str_starts_with($current, 'organizations') ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'organizations') ? '1' : '0.7' }}">corporate_fare</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'organizations') ? '1' : '0.7' }}">Organizations</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('programs.index') }}"
                style="{{ str_starts_with($current, 'programs') ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'programs') ? '1' : '0.7' }}">account_tree</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'programs') ? '1' : '0.7' }}">Programs</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('packages.index') }}"
                style="{{ str_starts_with($current, 'packages') ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'packages') ? '1' : '0.7' }}">inventory_2</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'packages') ? '1' : '0.7' }}">Packages</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('projects.index') }}"
                style="{{ str_starts_with($current, 'projects') ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'projects') ? '1' : '0.7' }}">assignment</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'projects') ? '1' : '0.7' }}">Projects</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_beneficiaries'))
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('beneficiaries.index') }}"
                style="{{ str_starts_with($current, 'beneficiaries') ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'beneficiaries') ? '1' : '0.7' }}">groups</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'beneficiaries') ? '1' : '0.7' }}">Beneficiaries</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('reports.index') }}"
                style="{{ str_starts_with($current, 'reports') ? 'background-color: var(--sidebar-active);' : '' }}">
                <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'reports') ? '1' : '0.7' }}">assessment</span>
                <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'reports') ? '1' : '0.7' }}">Reports</span>
            </a>
            @endif
            <div class="pt-4 mt-4 border-t border-white/10" style="border-color: var(--sidebar-hover)">
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('notifications.index') }}"
                    style="{{ str_starts_with($current, 'notifications') ? 'background-color: var(--sidebar-active);' : '' }}">
                    <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'notifications') ? '1' : '0.7' }}">notifications</span>
                    <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'notifications') ? '1' : '0.7' }}">Notifications</span>
                    <span id="sidebar-notif-badge" class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 leading-none hidden"></span>
                </a>
                
                @if(auth()->user()->hasPermissionTo('view_users'))
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('users.index') }}"
                    style="{{ str_starts_with($current, 'users') ? 'background-color: var(--sidebar-active);' : '' }}">
                    <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'users') ? '1' : '0.7' }}">people</span>
                    <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'users') ? '1' : '0.7' }}">User Management</span>
                </a>
                @endif

                @if(auth()->user()->hasPermissionTo('view_roles'))
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('roles.index') }}"
                    style="{{ str_starts_with($current, 'roles') ? 'background-color: var(--sidebar-active);' : '' }}">
                    <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'roles') ? '1' : '0.7' }}">security</span>
                    <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'roles') ? '1' : '0.7' }}">Roles & Permissions</span>
                </a>
                @endif

                <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group" href="{{ route('audit.index') }}"
                    style="{{ str_starts_with($current, 'audit') ? 'background-color: var(--sidebar-active);' : '' }}">
                    <span class="material-icons text-sm group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'audit') ? '1' : '0.7' }}">history</span>
                    <span class="text-sm font-medium group-hover:opacity-100" style="opacity: {{ str_starts_with($current, 'audit') ? '1' : '0.7' }}">Audit Logs</span>
                </a>
            </div>
        </nav>
        <div class="p-4 mt-auto">
            <div class="p-4 rounded-lg flex items-center gap-3" style="background-color: var(--sidebar-hover)">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: var(--sidebar-active)">
                    <span class="material-icons text-sm" style="color: var(--sidebar-text)">person</span>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-semibold truncate">{{ Auth::user()->name ?? 'Guest' }}</p>
                    <p class="text-[10px] truncate capitalize" style="opacity: 0.7">{{ str_replace('_', ' ', Auth::user()->role ?? 'guest') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:opacity-100 transition-opacity" style="opacity: 0.7; color: var(--sidebar-text)">
                        <span class="material-icons text-sm">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto w-full">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary">@yield('header', 'Dashboard')</h1>
                <p class="text-slate-500 text-sm">@yield('subheader', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleThemeDropdown()" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition-colors relative" title="Theme Settings">
                    <span class="material-icons text-lg">palette</span>
                </button>
                @yield('actions')
                <!-- Notification Bell with Dropdown -->
                <div class="relative" id="notification-wrapper">
                    <!-- Theme Dropdown -->
                    <div id="theme-dropdown" class="hidden absolute right-12 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 p-4 transform transition-all duration-200 opacity-0 scale-95 origin-top-right">
                        <h3 class="font-bold text-slate-800 text-sm mb-3">Theme Settings</h3>
                        
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-slate-500 mb-2">Accent Color</p>
                            <div class="grid grid-cols-5 gap-2">
                                <button onclick="setThemeColor('30 30 138')" class="w-8 h-8 rounded-full bg-[#1e1e8a] hover:ring-2 ring-offset-2 ring-[#1e1e8a]"></button>
                                <button onclick="setThemeColor('16 185 129')" class="w-8 h-8 rounded-full bg-emerald-500 hover:ring-2 ring-offset-2 ring-emerald-500"></button>
                                <button onclick="setThemeColor('139 92 246')" class="w-8 h-8 rounded-full bg-violet-500 hover:ring-2 ring-offset-2 ring-violet-500"></button>
                                <button onclick="setThemeColor('244 63 94')" class="w-8 h-8 rounded-full bg-rose-500 hover:ring-2 ring-offset-2 ring-rose-500"></button>
                                <button onclick="setThemeColor('245 158 11')" class="w-8 h-8 rounded-full bg-amber-500 hover:ring-2 ring-offset-2 ring-amber-500"></button>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-2">Sidebar Style</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="setSidebarTheme('solid')" class="px-2 py-1.5 text-xs font-medium bg-[#171719] text-white hover:bg-black rounded border border-slate-700">Modern (Default)</button>
                                <button onclick="setSidebarTheme('blue')" class="px-2 py-1.5 text-xs font-medium bg-[#1e1e8a] text-white hover:bg-blue-900 rounded border border-blue-900">Impact Blue</button>
                                <button onclick="setSidebarTheme('dark')" class="px-2 py-1.5 text-xs font-medium bg-slate-700 text-white hover:bg-slate-600 rounded border border-slate-600">Slate Dark</button>
                                <button onclick="setSidebarTheme('midnight')" class="px-2 py-1.5 text-xs font-medium bg-[#0f172a] text-white hover:bg-slate-800 rounded border border-slate-900">Midnight</button>
                            </div>
                        </div>
                    </div>

                    <button id="notification-bell" onclick="toggleNotificationDropdown()" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition-colors relative">
                        <span class="material-icons text-lg">notifications</span>
                        <span id="notif-badge" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white hidden transition-all"></span>
                        <span id="notif-count-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center leading-none hidden"></span>
                    </button>

                    <!-- Dropdown -->
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden transform transition-all duration-200 origin-top-right opacity-0 scale-95">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-primary/5 to-transparent">
                            <h3 class="font-bold text-slate-800 text-sm">Notifications</h3>
                            <div class="flex items-center gap-2">
                                <button onclick="markAllNotificationsRead()" class="text-xs text-primary font-semibold hover:underline">Mark all read</button>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-slate-400 hover:text-primary font-medium">View All</a>
                            </div>
                        </div>

                        <!-- Notification List -->
                        <div id="notification-list" class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            <div class="px-5 py-8 text-center">
                                <span class="material-icons text-slate-300 text-3xl">notifications_off</span>
                                <p class="text-sm text-slate-400 mt-2">Loading notifications...</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                            <a href="{{ route('notifications.index') }}" class="text-xs text-primary font-bold hover:underline flex items-center justify-center gap-1">
                                <span>See all notifications</span>
                                <span class="material-icons text-xs">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-icons">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-icons">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')

    <!-- Notification System JavaScript -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        let notifDropdownOpen = false;

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            notifDropdownOpen = !notifDropdownOpen;

            if (notifDropdownOpen) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
                fetchNotifications();
            } else {
                closeNotifDropdown();
            }
        }

        function closeNotifDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.remove('opacity-100', 'scale-100');
            dropdown.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 200);
            notifDropdownOpen = false;
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notification-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                closeNotifDropdown();
            }
        });

        function fetchNotifications() {
            fetch('{{ route("notifications.latest") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                renderNotifications(data.notifications);
                updateBadge(data.unread_count);
            })
            .catch(err => console.error('Notification fetch error:', err));
        }

        function updateBadge(count) {
            const dot = document.getElementById('notif-badge');
            const badge = document.getElementById('notif-count-badge');
            const sidebarBadge = document.getElementById('sidebar-notif-badge');

            if (count > 0) {
                dot.classList.remove('hidden');
                badge.classList.remove('hidden');
                badge.textContent = count > 99 ? '99+' : count;
                if (sidebarBadge) {
                    sidebarBadge.classList.remove('hidden');
                    sidebarBadge.textContent = count > 99 ? '99+' : count;
                }
            } else {
                dot.classList.add('hidden');
                badge.classList.add('hidden');
                if (sidebarBadge) sidebarBadge.classList.add('hidden');
            }
        }

        function getColorClasses(color) {
            const map = {
                'sky':     { bg: 'bg-sky-50',     text: 'text-sky-500' },
                'emerald': { bg: 'bg-emerald-50', text: 'text-emerald-500' },
                'amber':   { bg: 'bg-amber-50',   text: 'text-amber-500' },
                'rose':    { bg: 'bg-rose-50',     text: 'text-rose-500' },
                'red':     { bg: 'bg-red-50',      text: 'text-red-500' },
                'violet':  { bg: 'bg-violet-50',   text: 'text-violet-500' },
                'primary': { bg: 'bg-primary/10',  text: 'text-primary' },
            };
            return map[color] || map['primary'];
        }

        function timeAgo(dateStr) {
            const now = new Date();
            const date = new Date(dateStr);
            const seconds = Math.floor((now - date) / 1000);
            if (seconds < 60) return 'Just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + 'm ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + 'h ago';
            const days = Math.floor(hours / 24);
            if (days < 7) return days + 'd ago';
            return date.toLocaleDateString();
        }

        function renderNotifications(notifications) {
            const list = document.getElementById('notification-list');
            if (!notifications || notifications.length === 0) {
                list.innerHTML = `
                    <div class="px-5 py-8 text-center">
                        <span class="material-icons text-slate-300 text-3xl">notifications_off</span>
                        <p class="text-sm text-slate-400 mt-2">No notifications yet</p>
                    </div>`;
                return;
            }

            list.innerHTML = notifications.map(n => {
                const colors = getColorClasses(n.color);
                const isUnread = !n.read_at;
                return `
                <div class="flex gap-3 px-5 py-3.5 hover:bg-slate-50 transition-colors cursor-pointer ${isUnread ? 'bg-primary/[0.02]' : ''}" onclick="markNotifRead(${n.id}, '${n.link || ''}')">
                    <div class="w-9 h-9 rounded-full ${colors.bg} flex items-center justify-center shrink-0 mt-0.5">
                        <span class="material-icons ${colors.text} text-base">${n.icon || 'notifications'}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-semibold text-slate-700 ${isUnread ? '' : 'text-slate-500'}">${n.title}</p>
                            ${isUnread ? '<span class="w-2 h-2 bg-primary rounded-full shrink-0 mt-1"></span>' : ''}
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">${n.message}</p>
                        <p class="text-[10px] text-slate-400 mt-1">${timeAgo(n.created_at)}</p>
                    </div>
                </div>`;
            }).join('');
        }

        function markNotifRead(id, link) {
            fetch('/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => {
                if (link) {
                    window.location.href = link;
                } else {
                    fetchNotifications();
                }
            });
        }

        function markAllNotificationsRead() {
            fetch('{{ route("notifications.markAllRead") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => fetchNotifications());
        }

        // Load badge count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("notifications.latest") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => updateBadge(data.unread_count))
            .catch(() => {});
        });

        // Auto-refresh every 30 seconds
        setInterval(() => {
            fetch('{{ route("notifications.latest") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                updateBadge(data.unread_count);
                if (notifDropdownOpen) {
                    renderNotifications(data.notifications);
                }
            })
            .catch(() => {});
        }, 30000);

        // Theme Switcher Logic
        let themeDropdownOpen = false;
        function toggleThemeDropdown() {
            const dropdown = document.getElementById('theme-dropdown');
            themeDropdownOpen = !themeDropdownOpen;
            if(themeDropdownOpen) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
                closeNotifDropdown();
            } else {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => dropdown.classList.add('hidden'), 200);
            }
        }

        function setThemeColor(rgb) {
            document.documentElement.style.setProperty('--color-primary', rgb);
            localStorage.setItem('theme_color', rgb);
        }

        function setSidebarTheme(theme) {
            if(theme === 'solid') {
                document.documentElement.removeAttribute('data-sidebar');
                localStorage.removeItem('sidebar_theme');
            } else {
                document.documentElement.setAttribute('data-sidebar', theme);
                localStorage.setItem('sidebar_theme', theme);
            }
        }
    </script>
    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasRole('organization_admin')))
        @include('admin.chat.interface')
    @endif
</body>
</html>
