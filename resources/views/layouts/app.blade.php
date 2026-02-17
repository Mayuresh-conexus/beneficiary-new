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
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#1e1e8a",
                        "background-light": "#f6f6f8",
                        "background-dark": "#121220",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
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
    <aside class="w-64 bg-primary text-white flex flex-col transition-all duration-300 ease-in-out shrink-0 sticky top-0 h-screen">
        <div class="p-6 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded flex items-center justify-center">
                <span class="material-icons text-white">admin_panel_settings</span>
            </div>
            <span class="font-bold text-xl tracking-tight">ImpactNexus</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @php $current = Route::currentRouteName(); @endphp

            <a class="flex items-center gap-3 px-4 py-3 {{ $current === 'dashboard' ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('dashboard') }}">
                <span class="material-icons text-sm">dashboard</span>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            @if(auth()->user()->hasPermissionTo('view_organizations'))
            <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'organizations') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('organizations.index') }}">
                <span class="material-icons text-sm">corporate_fare</span>
                <span class="text-sm font-medium">Organizations</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'programs') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('programs.index') }}">
                <span class="material-icons text-sm">account_tree</span>
                <span class="text-sm font-medium">Programs</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'packages') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('packages.index') }}">
                <span class="material-icons text-sm">inventory_2</span>
                <span class="text-sm font-medium">Packages</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'projects') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('projects.index') }}">
                <span class="material-icons text-sm">assignment</span>
                <span class="text-sm font-medium">Projects</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_beneficiaries'))
            <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'beneficiaries') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('beneficiaries.index') }}">
                <span class="material-icons text-sm">groups</span>
                <span class="text-sm font-medium">Beneficiaries</span>
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('view_projects'))
            <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'reports') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('reports.index') }}">
                <span class="material-icons text-sm">assessment</span>
                <span class="text-sm font-medium">Reports</span>
            </a>
            @endif
            <div class="pt-4 mt-4 border-t border-white/10">
                <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'notifications') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('notifications.index') }}">
                    <span class="material-icons text-sm">notifications</span>
                    <span class="text-sm font-medium">Notifications</span>
                    <span id="sidebar-notif-badge" class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 leading-none hidden"></span>
                </a>
                
                @if(auth()->user()->hasPermissionTo('view_users'))
                <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'users') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('users.index') }}">
                    <span class="material-icons text-sm">people</span>
                    <span class="text-sm font-medium">User Management</span>
                </a>
                @endif

                @if(auth()->user()->hasPermissionTo('view_roles'))
                <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'roles') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('roles.index') }}">
                    <span class="material-icons text-sm">security</span>
                    <span class="text-sm font-medium">Roles & Permissions</span>
                </a>
                @endif

                <a class="flex items-center gap-3 px-4 py-3 {{ str_starts_with($current, 'audit') ? 'bg-white/10' : 'hover:bg-white/5 text-white/70 hover:text-white' }} rounded-lg transition-colors" href="{{ route('audit.index') }}">
                    <span class="material-icons text-sm">history</span>
                    <span class="text-sm font-medium">Audit Logs</span>
                </a>
            </div>
        </nav>
        <div class="p-4 mt-auto">
            <div class="bg-white/5 p-4 rounded-lg flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="material-icons text-white text-sm">person</span>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-semibold truncate">{{ Auth::user()->name ?? 'Guest' }}</p>
                    <p class="text-[10px] text-white/50 truncate capitalize">{{ str_replace('_', ' ', Auth::user()->role ?? 'guest') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/50 hover:text-white transition-colors">
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
                @yield('actions')
                <!-- Notification Bell with Dropdown -->
                <div class="relative" id="notification-wrapper">
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
    </script>
</body>
</html>
