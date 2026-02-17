// =====================================================================
//  FIELD CONNECT — SCREEN RENDERER (Splash, Login, Dashboard)
// =====================================================================

function navigate(screen, data) {
    AppState.currentScreen = screen;
    if (data) Object.assign(AppState, data);
    renderApp();
}

function renderApp() {
    const app = document.getElementById('app');
    const s = AppState.currentScreen;
    if (s === 'splash') app.innerHTML = splashScreen();
    else if (s === 'login') app.innerHTML = loginScreen();
    else if (s === 'dashboard') app.innerHTML = dashboardScreen();
    else if (s === 'projects') app.innerHTML = projectsScreen();
    else if (s === 'project-detail') app.innerHTML = projectDetailScreen();
    else if (s === 'add-beneficiary') app.innerHTML = addBeneficiaryScreen();
    else if (s === 'history') app.innerHTML = historyScreen();
    else if (s === 'beneficiary-detail') app.innerHTML = beneficiaryDetailScreen();
    else if (s === 'drafts') app.innerHTML = draftsScreen();
    bindEvents();
    updateOfflineBanner();
}

function timeAgo(d) {
    const s = Math.floor((Date.now() - new Date(d)) / 1000);
    if (s < 60) return 'Just now';
    if (s < 3600) return Math.floor(s / 60) + 'm ago';
    if (s < 86400) return Math.floor(s / 3600) + 'h ago';
    return Math.floor(s / 86400) + 'd ago';
}

function statusBadge(status) {
    const m = {
        approved: ['bg-green-100 text-green-700', 'check_circle', 'bg-green-500'],
        submitted: ['bg-yellow-100 text-yellow-700', 'schedule', 'bg-yellow-500'],
        under_review: ['bg-blue-100 text-blue-700', 'rate_review', 'bg-blue-500'],
        rejected: ['bg-red-100 text-red-700', 'error_outline', 'bg-red-500'],
        fraud: ['bg-orange-100 text-orange-700', 'warning', 'bg-orange-500'],
    };
    const [cls, icon, dot] = m[status] || m.submitted;
    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${cls}">
        <span class="w-1.5 h-1.5 rounded-full ${dot} mr-1.5"></span>${status.replace('_', ' ')}
    </span>`;
}

// --- SPLASH ---
function splashScreen() {
    setTimeout(() => navigate(AppState.token ? 'dashboard' : 'login'), 2500);
    return `<div class="screen active flex-col items-center justify-between h-full bg-background-light" style="display:flex">
        <div class="w-full h-12"></div>
        <div class="flex flex-col items-center justify-center flex-1 px-6 -mt-16">
            <div class="animate-logo-pulse relative mb-8 p-6 bg-white rounded-2xl shadow-xl shadow-primary/10 ring-1 ring-black/5 flex items-center justify-center w-32 h-32">
                <span class="material-icons-round text-6xl text-primary">volunteer_activism</span>
                <div class="absolute inset-0 rounded-2xl bg-primary/5"></div>
            </div>
            <h1 class="text-3xl font-bold text-primary tracking-tight text-center mb-2">Field Connect</h1>
            <p class="text-slate-500 text-sm font-medium text-center">Beneficiary Management System</p>
        </div>
        <div class="w-full max-w-xs px-8 pb-12 flex flex-col items-center gap-6">
            <div class="w-full flex flex-col gap-2">
                <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full animate-progress shadow-[0_0_10px_rgba(30,59,138,0.4)]"></div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                <span class="material-icons-round text-[10px]">lock</span>
                <span>Secure Connection</span>
            </div>
        </div>
    </div>`;
}

// --- LOGIN ---
function loginScreen() {
    return `<div class="screen active h-full flex flex-col bg-background-light" style="display:flex">
        <div class="w-full h-12"></div>
        <main class="flex-1 flex flex-col justify-center px-6 max-w-md mx-auto w-full pb-8">
            <div class="text-center mb-10">
                <div class="mx-auto h-16 w-16 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center shadow-lg mb-6">
                    <span class="material-icons-round text-white text-3xl">volunteer_activism</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-sm text-gray-500">Enter your credentials to access beneficiary data.</p>
            </div>
            <form id="login-form" class="space-y-6">
                <div id="login-error" class="hidden bg-red-50 text-red-700 text-sm p-3 rounded-lg"></div>
                <div class="float-input">
                    <input id="login-email" type="email" placeholder="user@ngo.org" required class="ios-input-shadow">
                    <span class="icon material-icons-round">person</span>
                </div>
                <div class="float-input relative">
                    <input id="login-password" type="password" placeholder="••••••••" required class="ios-input-shadow" style="padding-right:48px">
                    <span class="icon material-icons-round">lock</span>
                    <button type="button" id="toggle-pw" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 p-1">
                        <span class="material-icons-round text-xl">visibility_off</span>
                    </button>
                </div>
                <button id="login-btn" type="submit" class="w-full py-4 bg-primary hover:bg-primary-hover text-white font-semibold rounded-lg shadow-lg shadow-primary/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span id="login-text">Secure Login</span>
                    <div id="login-spinner" class="spinner hidden"></div>
                </button>
            </form>
        </main>
        <footer class="py-6 text-center">
            <div class="flex items-center justify-center gap-1 text-xs text-slate-400">
                <span class="material-icons-round text-xs">lock</span>
                <span>Field Connect v2.4.1</span>
            </div>
        </footer>
    </div>`;
}

// --- DASHBOARD ---
function dashboardScreen() {
    const u = AppState.user || {};
    const s = AppState.dashStats || {};
    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <div id="offline-banner" class="offline-banner"><span class="material-icons-round text-sm">cloud_off</span>Offline Mode</div>
        <header class="bg-white sticky top-0 z-20 px-6 py-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                            <span class="material-icons-round text-primary">person</span>
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Welcome back,</p>
                        <h1 class="text-lg font-bold text-slate-800 leading-tight">${u.name || 'Volunteer'}</h1>
                    </div>
                </div>
                <button onclick="logout()" class="p-2 rounded-full hover:bg-slate-100 transition-colors">
                    <span class="material-icons-round text-slate-500">logout</span>
                </button>
            </div>
        </header>
        <main class="screen-scroll no-scrollbar px-5 pt-6 pb-24 space-y-8">
            <section>
                <div class="flex justify-between items-end mb-4">
                    <h2 class="text-lg font-bold text-slate-800">Overview</h2>
                    <span class="text-xs font-medium text-slate-500">Last updated: Just now</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    ${statCard('assignment_ind', 'primary', s.assigned_projects || 0, 'Assigned Projects')}
                    ${statCard('pending_actions', 'yellow-500', s.pending_review || 0, 'Pending Review')}
                    ${statCard('verified', 'green-500', s.approved || 0, 'Approved')}
                    ${statCard('gpp_bad', 'red-500', (s.rejected || 0) + (s.fraud_flagged || 0), 'Rejected / Fraud')}
                </div>
            </section>
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-slate-800">Your Assigned Projects</h2>
                    <a onclick="navigate('projects')" class="text-sm font-semibold text-primary cursor-pointer">See All</a>
                </div>
                <div class="space-y-4" id="dash-projects">
                    <div class="text-center py-8 text-slate-400"><div class="spinner mx-auto" style="border-color:#cbd5e1;border-top-color:#1e3b8a"></div></div>
                </div>
            </section>
        </main>
        ${bottomNav('dashboard')}
    </div>`;
}

function statCard(icon, color, val, label) {
    const c = color === 'primary' ? 'text-primary bg-primary/10' : `text-${color} bg-${color}/10`;
    const bg = color === 'primary' ? 'text-primary' : `text-${color}`;
    return `<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-3 opacity-10"><span class="material-icons-round text-6xl ${bg}">${icon}</span></div>
        <div class="w-10 h-10 rounded-lg ${c} flex items-center justify-center mb-2">
            <span class="material-icons-round text-xl">${icon}</span>
        </div>
        <div><span class="block text-3xl font-bold text-slate-800">${val}</span>
        <span class="text-xs font-medium text-slate-500">${label}</span></div>
    </div>`;
}

function projectCard(p) {
    const pct = p.progress || 0;
    const col = pct > 50 ? 'bg-primary' : 'bg-amber-500';
    return `<div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-shadow cursor-pointer" onclick="openProject(${p.id})">
        <div class="flex justify-between items-start mb-3">
            <div class="flex gap-3">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-icons-round text-primary">assignment</span>
                </div>
                <div><h3 class="font-bold text-slate-800 leading-tight">${p.name}</h3>
                    <div class="flex items-center text-xs text-slate-500 mt-1">
                        <span class="material-icons-round text-sm mr-1">place</span>${p.location || 'N/A'}
                    </div>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-semibold">${p.status}</span>
        </div>
        <div class="mt-4"><div class="flex justify-between text-xs mb-1.5">
            <span class="text-slate-500">Progress</span><span class="font-semibold text-slate-700">${pct}%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2"><div class="${col} h-2 rounded-full" style="width:${pct}%"></div></div>
        <div class="flex justify-between mt-3 text-xs text-slate-500">
            <span>${p.stats?.total || 0} Beneficiaries</span>
        </div></div>
    </div>`;
}

function bottomNav(active) {
    const items = [
        ['dashboard', 'dashboard', 'Home'],
        ['projects', 'assignment', 'Projects'],
        ['history', 'history', 'History'],
        ['drafts', 'folder_open', 'Drafts'],
    ];
    return `<nav class="bottom-nav safe-area-bottom shrink-0">${items.map(([s, i, l]) =>
        `<a onclick="navigate('${s}')" class="${active === s ? 'active' : ''} cursor-pointer">
            <span class="material-icons-round">${i}</span><span>${l}</span>
        </a>`).join('')}</nav>`;
}
