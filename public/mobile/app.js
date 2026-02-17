// =====================================================================
//  FIELD CONNECT — MAIN APP (Event Binding & Data Loading)
// =====================================================================

// --- Event Binding ---
function bindEvents() {
    const s = AppState.currentScreen;

    // Login
    if (s === 'login') {
        const form = document.getElementById('login-form');
        if (form) form.onsubmit = handleLogin;
        const toggle = document.getElementById('toggle-pw');
        if (toggle) toggle.onclick = () => {
            const pw = document.getElementById('login-password');
            pw.type = pw.type === 'password' ? 'text' : 'password';
            toggle.querySelector('span').textContent = pw.type === 'password' ? 'visibility_off' : 'visibility';
        };
    }

    // Dashboard — load data
    if (s === 'dashboard') loadDashboard();
    if (s === 'projects') loadProjects();
    if (s === 'history') loadHistory();

    // Add beneficiary — GPS (start early so it's ready by step 3)
    if (s === 'add-beneficiary' && AppState.formStep === 1 && !AppState.formData.latitude) captureGPS();
    if (s === 'add-beneficiary' && AppState.formStep === 3) captureGPS();
}

// --- Login Handler ---
async function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const pw = document.getElementById('login-password').value;
    const btn = document.getElementById('login-btn');
    const txt = document.getElementById('login-text');
    const spin = document.getElementById('login-spinner');
    const err = document.getElementById('login-error');

    btn.disabled = true; txt.textContent = 'Signing in...'; spin.classList.remove('hidden'); err.classList.add('hidden');

    try {
        const res = await API.login(email, pw);
        saveAuth(res.data.token, res.data.user);
        showToast('Welcome, ' + res.data.user.name);
        navigate('dashboard');
    } catch (ex) {
        err.textContent = ex.message || 'Invalid credentials';
        err.classList.remove('hidden');
        btn.disabled = false; txt.textContent = 'Secure Login'; spin.classList.add('hidden');
    }
}

// --- Dashboard Data ---
async function loadDashboard() {
    try {
        const [dash, proj] = await Promise.all([API.dashboard(), API.projects()]);
        AppState.dashStats = dash.data;
        AppState.projects = proj.data;
        // Re-render stats
        const statSection = document.querySelector('#app .grid.grid-cols-2');
        if (statSection) {
            statSection.innerHTML = statCard('assignment_ind', 'primary', dash.data.assigned_projects, 'Assigned Projects') +
                statCard('pending_actions', 'yellow-500', dash.data.pending_review, 'Pending Review') +
                statCard('verified', 'green-500', dash.data.approved, 'Approved') +
                statCard('gpp_bad', 'red-500', (dash.data.rejected || 0) + (dash.data.fraud_flagged || 0), 'Rejected / Fraud');
        }
        // Render projects
        const pl = document.getElementById('dash-projects');
        if (pl) pl.innerHTML = proj.data.length ? proj.data.slice(0, 3).map(p => projectCard(p)).join('') : '<p class="text-center text-slate-500 py-8">No projects assigned yet.</p>';
    } catch (ex) {
        const pl = document.getElementById('dash-projects');
        if (pl) pl.innerHTML = '<p class="text-center text-red-500 py-4">Failed to load: ' + ex.message + '</p>';
    }
}

// --- Projects Data ---
async function loadProjects() {
    try {
        if (!AppState.projects.length) {
            const res = await API.projects();
            AppState.projects = res.data;
        }
        const el = document.getElementById('projects-list');
        if (el) el.innerHTML = AppState.projects.length ? AppState.projects.map(p => projectCard(p)).join('') : '<p class="text-center text-slate-500 py-8">No projects.</p>';
    } catch (ex) {
        const el = document.getElementById('projects-list');
        if (el) el.innerHTML = '<p class="text-center text-red-500 py-4">' + ex.message + '</p>';
    }
}

function openProject(id) {
    AppState.currentProject = AppState.projects.find(p => p.id === id);
    navigate('project-detail');
}

// --- History ---
async function loadHistory(status) {
    try {
        const res = await API.mySubmissions(status);
        AppState.submissions = res.data.data || res.data;
        renderHistoryList();
    } catch (ex) {
        const el = document.getElementById('history-list');
        if (el) el.innerHTML = '<p class="text-center text-red-500 py-4">' + ex.message + '</p>';
    }
}

function renderHistoryList() {
    const el = document.getElementById('history-list');
    if (!el) return;
    const subs = AppState.submissions;
    if (!subs || !subs.length) { el.innerHTML = '<p class="text-center text-slate-500 py-8">No submissions yet.</p>'; return; }
    const borderColors = { approved: 'bg-green-500', submitted: 'bg-yellow-500', under_review: 'bg-blue-500', rejected: 'bg-red-500', fraud: 'bg-orange-500' };
    el.innerHTML = subs.map(b => `
        <div class="bg-white rounded-lg p-4 shadow-sm border border-slate-100 active:scale-[0.99] transition-all cursor-pointer relative overflow-hidden" onclick="openBeneficiary(${b.id})">
            <div class="absolute left-0 top-0 bottom-0 w-1 ${borderColors[b.status] || 'bg-slate-300'} rounded-l-lg"></div>
            <div class="flex justify-between items-start mb-2">
                <div><h3 class="text-base font-bold text-slate-900 mb-0.5">${b.first_name} ${b.last_name}</h3>
                <p class="text-xs text-slate-500 font-medium">${b.project?.name || ''}</p></div>
                ${statusBadge(b.status)}
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                <div class="flex items-center text-xs text-slate-400"><span class="material-icons-round text-sm mr-1">schedule</span>${timeAgo(b.created_at)}</div>
            </div>
        </div>`).join('');
}

function filterHistory(status) {
    const buttons = document.querySelectorAll('#history-filters button');
    buttons.forEach(b => {
        b.className = b.className.replace(/bg-primary text-white shadow-md shadow-primary\/30/g, 'bg-white text-slate-600 border border-slate-200');
    });
    event.target.closest('button').className = event.target.closest('button').className.replace(/bg-white text-slate-600 border border-slate-200/g, 'bg-primary text-white shadow-md shadow-primary/30');
    loadHistory(status);
}

async function openBeneficiary(id) {
    try {
        const res = await API.show(id);
        AppState.currentBeneficiary = res.data;
        navigate('beneficiary-detail');
    } catch (ex) { showToast(ex.message, 'error'); }
}

// --- Add Beneficiary ---
// --- Add Beneficiary ---
async function startBeneficiary(projectId) {
    // Try to get packages from cached project data first (offline friendly)
    const cachedProject = AppState.projects.find(p => p.id === projectId);
    if (cachedProject && cachedProject.packages) {
        AppState.currentPackages = cachedProject.packages;
    } else {
        try {
            const res = await API.packages(projectId);
            AppState.currentPackages = res.data;
        } catch (e) {
            console.error('Failed to load packages', e);
            AppState.currentPackages = [];
        }
    }

    AppState.formData = { assigned_project_id: projectId, documents: [], package_ids: [] };
    AppState.formStep = 1;
    navigate('add-beneficiary');
}

function nextStep() {
    saveFormStep();
    if (AppState.formStep === 1) {
        const f = AppState.formData;
        if (!f.first_name || !f.last_name || !f.government_id) { showToast('Please fill all required fields', 'error'); return; }
    }
    AppState.formStep++;
    renderApp();
}

function prevStep() { saveFormStep(); AppState.formStep--; renderApp(); }

function saveFormStep() {
    if (AppState.formStep === 1) {
        AppState.formData.first_name = document.getElementById('f_first')?.value || '';
        AppState.formData.last_name = document.getElementById('f_last')?.value || '';
        AppState.formData.contact_number = document.getElementById('f_phone')?.value || '';
        AppState.formData.government_id = document.getElementById('f_govid')?.value || '';
        AppState.formData.date_of_birth = document.getElementById('f_dob')?.value || '';
        AppState.formData.address = document.getElementById('f_addr')?.value || '';
        const gender = document.querySelector('input[name="gender"]:checked');
        AppState.formData.gender = gender ? gender.value : '';

        // Capture packages
        const packageChecks = document.querySelectorAll('input[name="packages"]:checked');
        AppState.formData.package_ids = Array.from(packageChecks).map(c => parseInt(c.value));
    }
}

function handleFileUpload(type, input) {
    const file = input.files[0];
    if (!file) return;
    if (!AppState.formData.documents) AppState.formData.documents = [];
    AppState.formData.documents = AppState.formData.documents.filter(d => d.type !== type);
    AppState.formData.documents.push({ type, file, name: file.name, size: file.size });
    renderApp();
}

function removeDoc(type) {
    AppState.formData.documents = (AppState.formData.documents || []).filter(d => d.type !== type);
    renderApp();
}

function captureGPS() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            AppState.formData.latitude = pos.coords.latitude;
            AppState.formData.longitude = pos.coords.longitude;
            const el = document.getElementById('gps-display');
            if (el) el.innerHTML = `<span class="font-medium">Lat: ${pos.coords.latitude.toFixed(4)}, Long: ${pos.coords.longitude.toFixed(4)}</span><br><span class="text-xs text-slate-500">Accuracy: ±${Math.round(pos.coords.accuracy)}m</span>`;
        }, () => {
            AppState.formData.latitude = 0; AppState.formData.longitude = 0;
            const el = document.getElementById('gps-display');
            if (el) el.textContent = 'GPS unavailable — using default coordinates';
        });
    }
}

async function submitBeneficiary() {
    const certify = document.getElementById('certify');
    if (!certify?.checked) { showToast('Please certify the information', 'error'); return; }

    saveFormStep();
    const f = AppState.formData;

    // Build a clean payload — only fields the API expects (no documents/File objects)
    const payload = {
        first_name: f.first_name || '',
        last_name: f.last_name || '',
        government_id: f.government_id || '',
        assigned_project_id: parseInt(f.assigned_project_id, 10),
        package_ids: f.package_ids || [],
    };

    // Optional fields — only include if they have values
    if (f.contact_number) payload.contact_number = f.contact_number;
    if (f.address) payload.address = f.address;
    if (f.date_of_birth) payload.date_of_birth = f.date_of_birth;
    if (f.gender) payload.gender = f.gender;
    if (f.latitude != null && f.latitude !== '') payload.latitude = parseFloat(f.latitude);
    if (f.longitude != null && f.longitude !== '') payload.longitude = parseFloat(f.longitude);

    if (!AppState.isOnline) { saveDraft(f); navigate('drafts'); return; }

    try {
        const res = await API.submit(payload);
        const beneficiaryId = res.data?.id;

        // Upload documents separately (as FormData, not JSON)
        if (f.documents?.length && beneficiaryId) {
            for (const doc of f.documents) {
                const fd = new FormData();
                fd.append('beneficiary_id', beneficiaryId);
                fd.append('type', doc.type);
                fd.append('file', doc.file);
                try { await API.upload(fd); } catch (ue) { console.error('Upload failed:', ue); }
            }
        }

        showToast('Beneficiary submitted successfully!', 'success');
        AppState.formData = {};
        AppState.formStep = 1;
        navigate('history');
    } catch (ex) {
        if (ex.message.includes('network') || ex.message.includes('fetch')) { saveDraft(f); navigate('drafts'); }
        else showToast(ex.message, 'error');
    }
}

function editResubmit(id) {
    const b = AppState.currentBeneficiary;
    if (!b) return;
    AppState.formData = { ...b, documents: [] };
    AppState.formData._resubmit_id = id;
    AppState.formStep = 1;
    navigate('add-beneficiary');
}

function resumeDraft(draftId) {
    const draft = AppState.drafts.find(d => d.draft_id === draftId);
    if (!draft) return;
    AppState.formData = { ...draft };
    AppState.formStep = 1;
    navigate('add-beneficiary');
}

// --- Boot ---
document.addEventListener('DOMContentLoaded', () => renderApp());
