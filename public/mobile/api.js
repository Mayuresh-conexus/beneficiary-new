// =====================================================================
//  FIELD CONNECT — API CLIENT & STATE MANAGEMENT
// =====================================================================
const API_BASE = '/api/v1';

const AppState = {
    token: localStorage.getItem('fc_token') || null,
    user: JSON.parse(localStorage.getItem('fc_user') || 'null'),
    projects: [],
    submissions: [],
    drafts: JSON.parse(localStorage.getItem('fc_drafts') || '[]'),
    currentScreen: 'splash',
    isOnline: navigator.onLine,
    currentProject: null,
    currentBeneficiary: null,
    formData: {},
    formStep: 1,
};

// --- Offline Detection ---
window.addEventListener('online', () => { AppState.isOnline = true; updateOfflineBanner(); syncDrafts(); });
window.addEventListener('offline', () => { AppState.isOnline = false; updateOfflineBanner(); });

function updateOfflineBanner() {
    const b = document.getElementById('offline-banner');
    if (b) b.classList.toggle('show', !AppState.isOnline);
}

// --- API Helpers ---
async function api(method, path, body = null, isFormData = false) {
    const headers = { 'Accept': 'application/json' };
    if (AppState.token) headers['Authorization'] = 'Bearer ' + AppState.token;
    if (!isFormData) headers['Content-Type'] = 'application/json';

    const opts = { method, headers };
    if (body) opts.body = isFormData ? body : JSON.stringify(body);

    const res = await fetch(API_BASE + path, opts);
    const data = await res.json();
    if (res.status === 401) { logout(); throw new Error('Session expired'); }
    if (!res.ok) throw new Error(data.message || data.error || 'Request failed');
    return data;
}

const API = {
    login: (email, pw) => api('POST', '/login', { email, password: pw }),
    logout: () => api('POST', '/logout'),
    me: () => api('GET', '/me'),
    projects: () => api('GET', '/volunteer/projects'),
    dashboard: () => api('GET', '/volunteer/dashboard'),
    packages: (pid) => api('GET', `/project/${pid}/packages`),
    submit: (d) => api('POST', '/beneficiaries', d),
    mySubmissions: (s) => api('GET', '/beneficiaries/my-submissions' + (s ? '?status=' + s : '')),
    show: (id) => api('GET', `/beneficiary/${id}`),
    resubmit: (id, d) => api('PUT', `/beneficiary/${id}`, d),
    upload: (fd) => api('POST', '/upload', fd, true),
    syncStatus: () => api('GET', '/sync-status'),
};

// --- Auth ---
function saveAuth(token, user) {
    AppState.token = token;
    AppState.user = user;
    localStorage.setItem('fc_token', token);
    localStorage.setItem('fc_user', JSON.stringify(user));
}

function logout() {
    if (AppState.token) API.logout().catch(() => { });
    AppState.token = null;
    AppState.user = null;
    localStorage.removeItem('fc_token');
    localStorage.removeItem('fc_user');
    navigate('login');
}

// --- Drafts (Offline Storage) ---
function saveDraft(data) {
    data.draft_id = Date.now();
    data.draft_time = new Date().toISOString();
    AppState.drafts.push(data);
    localStorage.setItem('fc_drafts', JSON.stringify(AppState.drafts));
    showToast('Draft saved locally', 'warning');
}

function deleteDraft(id) {
    AppState.drafts = AppState.drafts.filter(d => d.draft_id !== id);
    localStorage.setItem('fc_drafts', JSON.stringify(AppState.drafts));
}

async function syncDrafts() {
    if (!AppState.isOnline || !AppState.drafts.length) return;
    for (const draft of [...AppState.drafts]) {
        try {
            await API.submit(draft);
            deleteDraft(draft.draft_id);
            showToast('Draft synced: ' + draft.first_name, 'success');
        } catch (e) { /* retry next time */ }
    }
}

// --- Toast ---
function showToast(msg, type = 'success') {
    let t = document.getElementById('toast');
    if (!t) { t = document.createElement('div'); t.id = 'toast'; t.className = 'toast'; document.body.appendChild(t); }
    t.textContent = msg;
    t.className = 'toast ' + type;
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => t.classList.remove('show'), 3000);
}
