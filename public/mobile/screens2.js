// =====================================================================
//  FIELD CONNECT — ADDITIONAL SCREENS (Projects, Add Beneficiary, History, Drafts, Detail)
// =====================================================================

// --- PROJECTS LIST ---
function projectsScreen() {
    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <div id="offline-banner" class="offline-banner"><span class="material-icons-round text-sm">cloud_off</span>Offline Mode</div>
        <header class="bg-white sticky top-0 z-20 px-5 py-4 shadow-sm flex items-center justify-between">
            <button onclick="navigate('dashboard')" class="p-2 -ml-2 rounded-full hover:bg-slate-100"><span class="material-icons-round text-primary">chevron_left</span></button>
            <h1 class="text-lg font-semibold text-slate-900">My Projects</h1><div class="w-10"></div>
        </header>
        <main class="screen-scroll no-scrollbar px-5 py-6 pb-24 space-y-4" id="projects-list">
            <div class="text-center py-8 text-slate-400"><div class="spinner mx-auto" style="border-color:#cbd5e1;border-top-color:#1e3b8a"></div></div>
        </main>
        ${bottomNav('projects')}
    </div>`;
}

// --- PROJECT DETAIL ---
function projectDetailScreen() {
    const p = AppState.currentProject || {};
    const pkgs = p.packages || [];
    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-md px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <button onclick="navigate('projects')" class="p-2 -ml-2 rounded-full hover:bg-gray-100"><span class="material-icons-round text-primary text-2xl">chevron_left</span></button>
            <h1 class="text-lg font-semibold text-slate-900">Project Details</h1><div class="w-10"></div>
        </header>
        <main class="screen-scroll no-scrollbar pb-32">
            <div class="p-5"><div class="bg-primary rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 left-0 -mb-6 -ml-6 w-32 h-32 bg-blue-400/20 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <span class="px-2.5 py-0.5 rounded bg-white/20 text-xs font-medium backdrop-blur-sm">${p.program || ''}</span>
                        <span class="bg-green-400/20 text-green-100 text-xs px-2 py-0.5 rounded-full flex items-center gap-1 border border-green-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>${p.status || 'Active'}
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold mb-1 leading-tight">${p.name || 'Project'}</h2>
                    <p class="text-blue-100 text-sm font-medium flex items-center gap-1"><span class="material-icons-round text-sm">corporate_fare</span>${p.organization || ''}</p>
                    <div class="mt-5"><div class="flex justify-between text-xs text-blue-100 mb-1"><span>Progress</span><span>${p.progress || 0}%</span></div>
                    <div class="w-full bg-blue-900/40 rounded-full h-1.5"><div class="bg-blue-300 h-1.5 rounded-full" style="width:${p.progress || 0}%"></div></div></div>
                </div>
            </div></div>
            <div class="px-5 grid grid-cols-2 gap-3 mb-6">
                <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm"><div class="p-2 bg-blue-50 rounded-lg mb-2 text-primary w-fit"><span class="material-icons-round text-xl">people</span></div>
                    <span class="text-2xl font-bold">${p.stats?.total || 0}</span><span class="block text-xs text-slate-500">Beneficiaries</span></div>
                <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm"><div class="p-2 bg-orange-50 rounded-lg mb-2 text-orange-600 w-fit"><span class="material-icons-round text-xl">inventory_2</span></div>
                    <span class="text-2xl font-bold">${pkgs.length}</span><span class="block text-xs text-slate-500">Packages</span></div>
            </div>
            ${pkgs.length ? `<div class="mb-6"><div class="px-5 mb-3 flex justify-between items-center"><h3 class="font-semibold text-slate-800">Available Packages</h3></div>
            <div class="flex overflow-x-auto px-5 pb-4 gap-3 no-scrollbar">${pkgs.map(pk => `
                <div class="shrink-0 w-36 bg-white p-3 rounded-lg border border-gray-200 shadow-sm cursor-pointer hover:border-primary/50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mb-2 text-primary"><span class="material-icons-round text-lg">inventory_2</span></div>
                    <p class="text-slate-800 text-sm font-semibold leading-tight">${pk.name}</p>
                    <p class="text-slate-500 text-xs mt-0.5">${pk.frequency || pk.type || ''}</p>
                </div>`).join('')}</div></div>` : ''}
        </main>
        <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-gray-100 p-4 pb-8 z-50">
            <button onclick="startBeneficiary(${p.id})" class="w-full bg-primary hover:bg-primary-hover text-white font-semibold py-4 px-6 rounded-lg shadow-lg shadow-blue-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <span class="material-icons-round">person_add</span>Add Beneficiary
            </button>
        </div>
    </div>`;
}

// --- ADD BENEFICIARY (multi-step) ---
function addBeneficiaryScreen() {
    const step = AppState.formStep || 1;
    const totalSteps = 3;
    const pct = Math.round((step / totalSteps) * 100);
    const titles = ['Personal Info', 'Documents', 'Review & Submit'];
    const backAction = step > 1 ? 'prevStep()' : "navigate('project-detail')";
    const nextAction = step < totalSteps ? 'nextStep()' : 'submitBeneficiary()';
    const nextLabel = step < totalSteps
        ? 'Next Step <span class="material-icons-round text-sm">arrow_forward</span>'
        : 'Submit for Approval <span class="material-icons-round text-sm">send</span>';
    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <header class="bg-white px-5 pt-5 pb-4 z-20 sticky top-0">
            <div class="flex items-center justify-between">
                <button onclick="${backAction}" class="p-2 -ml-2 rounded-full hover:bg-slate-100"><span class="material-icons-round text-slate-600">arrow_back_ios_new</span></button>
                <h1 class="text-lg font-semibold text-slate-900">Add Beneficiary</h1><div class="w-10"></div>
            </div>
            <div class="mt-4"><div class="flex justify-between items-end mb-2">
                <span class="text-sm font-semibold text-primary">${titles[step - 1]}</span>
                <span class="text-xs text-slate-500 font-medium">Step ${step} of ${totalSteps}</span>
            </div>
            <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden"><div class="h-full bg-primary rounded-full transition-all" style="width:${pct}%"></div></div></div>
        </header>
        <main class="screen-scroll no-scrollbar px-6 pb-28 pt-4">${step === 1 ? formStep1() : step === 2 ? formStep2() : formStep3()}</main>
        <footer class="bg-white border-t border-slate-100 p-4 shrink-0 safe-area-bottom z-10">
            <div class="flex gap-3">
                ${step > 1 ? '<button onclick="prevStep()" class="px-6 py-3.5 rounded-lg border border-slate-300 text-slate-700 font-semibold text-sm w-1/3">Previous</button>' : ''}
                <button onclick="${nextAction}" class="flex-1 py-3.5 rounded-lg bg-primary hover:bg-primary-hover text-white font-semibold text-sm shadow-lg flex items-center justify-center gap-2">
                    ${nextLabel}
                </button>
            </div>
        </footer>
    </div>`;
}

function formStep1() {
    const f = AppState.formData;
    return `<div class="animate-fadeIn"><div class="mb-6"><h2 class="text-2xl font-bold text-slate-900 mb-2">Who are we helping?</h2>
        <p class="text-slate-500 text-sm leading-relaxed">Please enter the beneficiary's primary details accurately.</p></div>
        <form id="step1-form" class="space-y-5">
            <div class="float-input"><input id="f_first" type="text" placeholder="First Name" value="${f.first_name || ''}" required>
                <span class="icon material-icons-round">person_outline</span></div>
            <div class="float-input"><input id="f_last" type="text" placeholder="Last Name" value="${f.last_name || ''}" required>
                <span class="icon material-icons-round">person_outline</span></div>
            <div class="float-input"><input id="f_phone" type="tel" placeholder="Phone Number" value="${f.contact_number || ''}">
                <span class="icon material-icons-round">phone</span></div>
            <div class="float-input"><input id="f_govid" type="text" placeholder="Government ID" value="${f.government_id || ''}" required>
                <span class="icon material-icons-round">badge</span></div>
            <div class="float-input"><input id="f_dob" type="date" placeholder="Date of Birth" value="${f.date_of_birth || ''}">
                <span class="icon material-icons-round">event</span></div>
            <div><label class="text-xs font-semibold text-slate-500 uppercase tracking-wider ml-1 block mb-2">Gender</label>
                <div class="gender-group">
                    <label><input type="radio" name="gender" value="male" ${f.gender === 'male' ? 'checked' : ''}><span>Male</span></label>
                    <label><input type="radio" name="gender" value="female" ${f.gender === 'female' ? 'checked' : ''}><span>Female</span></label>
                    <label><input type="radio" name="gender" value="other" ${f.gender === 'other' ? 'checked' : ''}><span>Other</span></label>
                </div>
            </div>
            <div class="float-input"><textarea id="f_addr" placeholder="Current Address" rows="3" style="padding-left:44px;resize:none">${f.address || ''}</textarea>
                <span class="icon material-icons-round" style="top:24px">place</span></div>
                
            ${AppState.currentPackages && AppState.currentPackages.length ? `
            <div>
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider ml-1 block mb-3">Select Packages</label>
                <div class="space-y-3">
                    ${AppState.currentPackages.map(p => `
                    <label class="flex items-center p-3 border border-slate-200 rounded-lg bg-white shadow-sm active:scale-[0.99] transition-transform cursor-pointer">
                        <input type="checkbox" name="packages" value="${p.id}" class="w-5 h-5 text-primary rounded focus:ring-primary border-gray-300" 
                            ${(f.package_ids || []).includes(p.id) ? 'checked' : ''}>
                        <div class="ml-3 flex-1">
                            <span class="block text-sm font-medium text-slate-900">${p.name}</span>
                            <span class="block text-xs text-slate-500">${p.frequency || p.type || 'One-time'}</span>
                        </div>
                    </label>`).join('')}
                </div>
            </div>` : ''}
        </form>
        <div class="flex items-start gap-2 bg-blue-50 p-3 rounded-lg border border-blue-100 mt-6">
            <span class="material-icons-round text-primary text-sm mt-0.5">info</span>
            <p class="text-xs text-slate-600 leading-tight">This information is confidential and will only be used for program enrollment.</p>
        </div></div>`;
}

function formStep2() {
    const docs = AppState.formData.documents || [];
    const types = [
        { key: 'id_proof', label: 'ID Proof', icon: 'badge', sub: 'National ID / Voter Card' },
        { key: 'income_proof', label: 'Income Proof', icon: 'attach_money', sub: 'Ration Card / Pay Slip' },
        { key: 'photo', label: 'Beneficiary Photo', icon: 'face', sub: 'Clear face photo required' }
    ];
    return `<div class="animate-fadeIn"><div class="mb-6"><h2 class="text-xl font-bold text-slate-900 mb-1">Document Upload</h2>
        <p class="text-sm text-slate-500">Please provide clear photos of the required proof documents.</p></div>
        ${types.map(t => {
        const uploaded = docs.find(d => d.type === t.key);
        if (uploaded) {
            return `<div class="doc-card uploaded">
                    <div class="flex justify-between items-start mb-4"><div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center"><span class="material-icons-round text-xl">${t.icon}</span></div>
                        <div><h3 class="font-bold text-slate-900">${t.label}</h3><p class="text-xs text-slate-500">${uploaded.name}</p></div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] uppercase font-bold tracking-wider rounded">Done</span></div>
                    <div class="bg-slate-50 rounded-lg p-3 flex gap-3 items-center">
                        <div class="w-16 h-16 shrink-0 rounded bg-slate-200 flex items-center justify-center"><span class="material-icons-round text-slate-400">image</span></div>
                        <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-800 truncate">${uploaded.name}</p><p class="text-xs text-slate-500 mt-1">${(uploaded.size / 1024 / 1024).toFixed(1)} MB</p></div>
                        <button onclick="removeDoc('${t.key}')" class="p-2 text-red-500 hover:bg-red-50 rounded-full"><span class="material-icons-round">delete_outline</span></button>
                    </div>
                </div>`;
        } else {
            return `<div class="doc-card">
                    <div class="flex justify-between items-start mb-4"><div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-primary flex items-center justify-center"><span class="material-icons-round text-xl">${t.icon}</span></div>
                        <div><h3 class="font-bold text-slate-900">${t.label}</h3><p class="text-xs text-slate-500">${t.sub}</p></div>
                    </div>
                    <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] uppercase font-bold tracking-wider rounded">Required</span></div>
                    <div class="upload-area" onclick="document.getElementById('file_${t.key}').click()">
                        <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center mb-3 mx-auto text-slate-400"><span class="material-icons-round">add_a_photo</span></div>
                        <p class="text-sm font-medium text-slate-700 mb-3">Tap to capture or upload</p>
                        <input type="file" id="file_${t.key}" accept="image/*,application/pdf" class="hidden" onchange="handleFileUpload('${t.key}', this)">
                        <div class="flex gap-3">
                            <button type="button" onclick="event.stopPropagation();document.getElementById('file_${t.key}').click()" class="flex-1 bg-primary text-white py-2.5 px-4 rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                                <span class="material-icons-round text-sm">camera_alt</span>Camera</button>
                            <button type="button" onclick="event.stopPropagation();document.getElementById('file_${t.key}').click()" class="flex-1 bg-white border border-slate-200 text-slate-700 py-2.5 px-4 rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                                <span class="material-icons-round text-sm">image</span>Gallery</button>
                        </div>
                    </div>
                </div>`;
        }
    }).join('')}
        <div class="flex gap-3 bg-primary/10 p-4 rounded-lg mt-4"><span class="material-icons-round text-primary text-sm mt-0.5">info</span>
        <p class="text-xs text-slate-600 leading-relaxed">Documents are encrypted and stored securely.</p></div></div>`;
}

function formStep3() {
    const f = AppState.formData;
    const docs = f.documents || [];
    return `<div class="animate-fadeIn"><div class="mb-4"><h2 class="text-2xl font-bold text-slate-900 mb-1">Review & Submit</h2>
        <p class="text-sm text-slate-500">Please verify all details before submitting for approval.</p></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-2"><span class="material-icons-round text-primary text-xl">person</span><h3 class="font-semibold text-gray-800">Personal Details</h3></div>
                <button onclick="AppState.formStep=1;renderApp()" class="text-primary text-sm font-semibold">Edit</button>
            </div>
            <div class="p-5 grid grid-cols-2 gap-y-4 gap-x-2">
                <div><p class="text-xs text-gray-500 uppercase font-medium mb-1">Full Name</p><p class="text-sm font-medium text-gray-900">${f.first_name || ''} ${f.last_name || ''}</p></div>
                <div><p class="text-xs text-gray-500 uppercase font-medium mb-1">Gender</p><p class="text-sm font-medium text-gray-900">${f.gender || 'N/A'}</p></div>
                <div><p class="text-xs text-gray-500 uppercase font-medium mb-1">Phone</p><p class="text-sm font-medium text-gray-900">${f.contact_number || 'N/A'}</p></div>
                <div><p class="text-xs text-gray-500 uppercase font-medium mb-1">Gov ID</p><p class="text-sm font-medium text-gray-900">${f.government_id || 'N/A'}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-500 uppercase font-medium mb-1">Address</p><p class="text-sm font-medium text-gray-900">${f.address || 'N/A'}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-2"><span class="material-icons-round text-primary text-xl">description</span><h3 class="font-semibold text-gray-800">Documents</h3></div>
                <button onclick="AppState.formStep=2;renderApp()" class="text-primary text-sm font-semibold">Edit</button>
            </div>
            <div class="p-5 space-y-3">${docs.length ? docs.map(d => `
                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3"><div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center"><span class="material-icons-round text-gray-500">image</span></div>
                    <div><p class="text-sm font-medium text-gray-900 truncate">${d.name}</p><p class="text-xs text-gray-500">${(d.size / 1024 / 1024).toFixed(1)} MB</p></div></div>
                    <span class="material-icons-round text-green-500 text-lg">check_circle</span>
                </div>`).join('') : '<p class="text-sm text-slate-500">No documents uploaded</p>'}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-2"><span class="material-icons-round text-primary text-xl">place</span><h3 class="font-semibold text-gray-800">Location</h3></div>
            </div>
            <div class="p-5"><p class="text-sm text-slate-800" id="gps-display">Capturing GPS...</p></div>
        </div>
        <div class="flex items-start gap-3 mt-4"><div class="flex items-center h-5">
            <input type="checkbox" id="certify" class="h-5 w-5 text-primary border-gray-300 rounded focus:ring-primary">
        </div><div class="text-sm"><label for="certify" class="font-medium text-gray-700">I certify this information is accurate</label>
        <p class="text-gray-500 text-xs">By submitting, you confirm the beneficiary meets all eligibility criteria.</p></div></div></div>`;
}

// --- HISTORY ---
function historyScreen() {
    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <div id="offline-banner" class="offline-banner"><span class="material-icons-round text-sm">cloud_off</span>Offline Mode</div>
        <header class="px-5 pt-5 pb-4 bg-white sticky top-0 z-20 shadow-sm border-b border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <button onclick="navigate('dashboard')" class="p-2 -ml-2 rounded-full hover:bg-slate-100"><span class="material-icons-round">arrow_back_ios_new</span></button>
                <h1 class="text-xl font-bold text-center flex-grow text-slate-800 pr-8">Submission History</h1>
                <div class="w-8"></div>
            </div>
            <div class="relative"><span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="material-icons-round text-slate-400">search</span></span>
            <input id="history-search" type="text" class="block w-full pl-10 pr-3 py-2.5 bg-slate-100 border-none rounded-lg text-sm text-slate-800 placeholder-slate-500 focus:ring-2 focus:ring-primary focus:bg-white" placeholder="Search beneficiary name..."></div>
        </header>
        <div class="px-5 py-3 bg-background-light z-10 overflow-x-auto no-scrollbar whitespace-nowrap" id="history-filters">
            <button onclick="filterHistory('')" class="active inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-primary text-white shadow-md shadow-primary/30 mr-2">All</button>
            <button onclick="filterHistory('submitted')" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white text-slate-600 border border-slate-200 mr-2">Pending</button>
            <button onclick="filterHistory('approved')" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white text-slate-600 border border-slate-200 mr-2">Approved</button>
            <button onclick="filterHistory('rejected')" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white text-slate-600 border border-slate-200 mr-2">Rejected</button>
        </div>
        <main class="screen-scroll no-scrollbar px-5 pb-24 pt-2 space-y-3" id="history-list">
            <div class="text-center py-8 text-slate-400"><div class="spinner mx-auto" style="border-color:#cbd5e1;border-top-color:#1e3b8a"></div></div>
        </main>
        <button onclick="navigate('project-detail')" class="fab"><span class="material-icons-round">add</span></button>
        ${bottomNav('history')}
    </div>`;
}

// --- BENEFICIARY DETAIL ---
function beneficiaryDetailScreen() {
    const b = AppState.currentBeneficiary || {};
    const reviews = b.reviews || [];
    const lastReview = reviews[0];
    const isRejected = b.status === 'rejected' || b.status === 'fraud';
    const isApproved = b.status === 'approved';

    // Build icon circle class
    let iconCircleClass = 'bg-yellow-100 text-yellow-600';
    let iconName = 'pending';
    if (isRejected) { iconCircleClass = 'bg-red-100 text-red-500'; iconName = 'gpp_bad'; }
    else if (isApproved) { iconCircleClass = 'bg-green-100 text-green-500'; iconName = 'verified'; }

    // Manager remarks block
    let remarksHtml = '';
    if (lastReview && isRejected) {
        remarksHtml = `<div class="px-5 mb-6"><div class="bg-red-50 border border-red-100 rounded-xl p-5 shadow-sm">
            <div class="flex items-start gap-3"><span class="material-icons-round text-red-500 mt-0.5">feedback</span>
            <div class="space-y-1"><h3 class="text-sm font-bold text-red-900 uppercase tracking-wide">Manager Remarks</h3>
            <p class="text-slate-700 leading-relaxed text-sm">"${lastReview.remarks || 'No remarks'}"</p>
            <div class="pt-2 flex items-center gap-2"><span class="text-xs font-medium text-slate-500">Reviewer: ${lastReview.manager?.name || 'Manager'}</span></div></div></div></div></div>`;
    }

    // Resubmit button
    let resubmitHtml = '';
    if (isRejected) {
        resubmitHtml = `<div class="absolute bottom-0 left-0 w-full bg-white border-t border-slate-100 p-4 pb-8 z-20">
            <button onclick="editResubmit(${b.id})" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2">
                <span class="material-icons-round">edit</span>Edit &amp; Resubmit</button></div>`;
    }

    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <header class="bg-white sticky top-0 z-20 px-4 py-4 border-b border-slate-100 flex items-center justify-between">
            <button onclick="navigate('history')" class="p-2 -ml-2 rounded-full hover:bg-slate-50"><span class="material-icons-round">arrow_back</span></button>
            <h1 class="text-lg font-bold text-primary">Submission #${b.id || '...'}</h1><div class="w-10"></div>
        </header>
        <main class="screen-scroll no-scrollbar pb-28">
            <div class="px-5 pt-6 pb-4 flex flex-col items-center text-center space-y-2">
                <div class="w-16 h-16 rounded-full ${iconCircleClass} flex items-center justify-center mb-1">
                    <span class="material-icons-round text-3xl">${iconName}</span>
                </div>
                <h2 class="text-2xl font-bold text-slate-900">${b.first_name || ''} ${b.last_name || ''}</h2>
                ${statusBadge(b.status || 'submitted')}
            </div>
            ${remarksHtml}
            <div class="px-5 mb-6">
                <h3 class="text-base font-bold text-primary mb-4">Details</h3>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 grid grid-cols-2 gap-4">
                    <div><label class="text-xs text-slate-500 uppercase font-medium">Name</label><p class="text-sm font-medium text-slate-800">${b.first_name || ''} ${b.last_name || ''}</p></div>
                    <div><label class="text-xs text-slate-500 uppercase font-medium">Gov ID</label><p class="text-sm font-medium text-slate-800">${b.government_id || 'N/A'}</p></div>
                    <div><label class="text-xs text-slate-500 uppercase font-medium">Gender</label><p class="text-sm font-medium text-slate-800">${b.gender || 'N/A'}</p></div>
                    <div><label class="text-xs text-slate-500 uppercase font-medium">Phone</label><p class="text-sm font-medium text-slate-800">${b.contact_number || 'N/A'}</p></div>
                    <div class="col-span-2"><label class="text-xs text-slate-500 uppercase font-medium">GPS</label><p class="text-sm font-medium text-slate-800">${b.latitude || 'N/A'}, ${b.longitude || 'N/A'}</p></div>
                </div>
            </div>
        </main>
        ${resubmitHtml}
    </div>`;
}

// --- DRAFTS ---
function draftsScreen() {
    const onlineClass = AppState.isOnline ? 'text-green-500' : 'text-slate-400';
    const onlineIcon = AppState.isOnline ? 'wifi' : 'wifi_off';
    const onlineLabel = AppState.isOnline ? 'Connected' : 'Waiting for Network';
    const canSync = AppState.isOnline && AppState.drafts.length;
    const syncBtnClass = canSync ? 'bg-primary text-white' : 'bg-slate-200 text-slate-500 cursor-not-allowed';
    const syncIcon = AppState.isOnline ? 'sync' : 'sync_disabled';

    let draftsHtml = '';
    if (AppState.drafts.length) {
        draftsHtml = AppState.drafts.map(d => `
            <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200 hover:border-primary/50 transition-all">
                <div class="flex items-start gap-4"><div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><span class="material-icons-round text-primary">person</span></div>
                <div class="flex-1"><h3 class="font-bold text-slate-900">${d.first_name || 'New'} ${d.last_name || 'Beneficiary'}</h3>
                <p class="text-xs text-slate-500 mt-1">ID: ${d.government_id || 'Pending'}</p>
                <div class="flex items-center gap-1 mt-3 text-xs text-slate-500"><span class="material-icons-round text-sm">schedule</span>${timeAgo(d.draft_time)}</div></div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Draft</span></div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between">
                    <button onclick="deleteDraft(${d.draft_id});renderApp()" class="text-red-500 text-sm font-semibold flex items-center gap-1"><span class="material-icons-round text-sm">delete</span>Delete</button>
                    <button onclick="resumeDraft(${d.draft_id})" class="text-primary font-semibold text-sm flex items-center gap-1">Resume <span class="material-icons-round text-sm">arrow_forward</span></button>
                </div>
            </div>`).join('');
    } else {
        draftsHtml = '<div class="text-center py-12 text-slate-400"><span class="material-icons-round text-5xl block mb-4">folder_off</span><p class="text-sm">No local drafts</p></div>';
    }

    return `<div class="screen active h-full flex flex-col" style="display:flex">
        <div id="offline-banner" class="offline-banner"><span class="material-icons-round text-sm">cloud_off</span>Offline Mode</div>
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20 px-4 h-14 flex items-center justify-between">
            <button onclick="navigate('dashboard')" class="text-slate-500"><span class="material-icons-round">arrow_back_ios_new</span></button>
            <h1 class="text-lg font-bold">Drafts &amp; Offline</h1>
            <button onclick="syncDrafts()" class="text-primary"><span class="material-icons-round">sync</span></button>
        </header>
        <main class="screen-scroll no-scrollbar px-4 py-6 pb-24">
            <div class="bg-white rounded-xl p-5 mb-6 shadow-sm border border-slate-100 flex flex-col items-center text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center"><span class="material-icons-round text-3xl ${onlineClass}">${onlineIcon}</span></div>
                <h2 class="text-lg font-bold text-slate-900">${onlineLabel}</h2>
                <p class="text-sm text-slate-500">Last synced: ${AppState.lastSync || 'Unknown'}</p>
                <button onclick="syncDrafts()" class="w-full mt-2 py-3 px-4 ${syncBtnClass} font-semibold rounded-lg flex items-center justify-center gap-2" ${!canSync ? 'disabled' : ''}>
                    <span class="material-icons-round text-lg">${syncIcon}</span>Sync Now</button>
            </div>
            <div class="flex items-center justify-between mb-4"><h2 class="text-xl font-bold text-primary">Local Drafts <span class="ml-1 text-sm font-medium text-slate-500 bg-slate-200 px-2 py-0.5 rounded-full align-middle">${AppState.drafts.length}</span></h2></div>
            <div class="space-y-4">${draftsHtml}</div>
        </main>
        ${bottomNav('drafts')}
    </div>`;
}
