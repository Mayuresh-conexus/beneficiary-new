@extends('layouts.app')
@section('title', $beneficiary->first_name . ' ' . $beneficiary->last_name)
@section('header', 'Beneficiary Review')
@section('subheader', 'ID: #BEN-' . $beneficiary->id . ' • ' . ucwords(str_replace('_', ' ', $beneficiary->status)))

@section('content')
@php $sc = ['approved'=>'bg-emerald-100 text-emerald-700','fraud'=>'bg-rose-100 text-rose-700','submitted'=>'bg-amber-100 text-amber-700','under_review'=>'bg-sky-100 text-sky-700','rejected'=>'bg-red-100 text-red-700','draft'=>'bg-slate-100 text-slate-500']; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Profile + Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-start gap-6">
                <div class="w-20 h-20 rounded-xl bg-primary/10 flex items-center justify-center">
                    <span class="material-icons text-primary text-3xl">person</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-slate-800">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h2>
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $sc[$beneficiary->status] ?? 'bg-slate-100' }}">{{ str_replace('_', ' ', $beneficiary->status) }}</span>
                    </div>
                    <p class="text-sm text-slate-500">{{ $beneficiary->gender ?? 'N/A' }} • Gov ID: {{ $beneficiary->government_id }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Contact</p>
                <p class="text-sm font-bold text-slate-700">{{ $beneficiary->contact_number ?? '—' }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">DOB</p>
                <p class="text-sm font-bold text-slate-700">{{ $beneficiary->date_of_birth?->format('M d, Y') ?? '—' }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Location</p>
                <p class="text-sm font-bold text-slate-700">{{ $beneficiary->latitude }}, {{ $beneficiary->longitude }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Address</p>
                <p class="text-sm font-bold text-slate-700">{{ $beneficiary->address ?? '—' }}</p>
            </div>
        </div>

        <!-- Project & Organization -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4">Assignment Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400 mb-1">Organization</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $beneficiary->project->program->organization->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400 mb-1">Program</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $beneficiary->project->program->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400 mb-1">Project</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $beneficiary->project->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400 mb-1">Submitted By</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $beneficiary->submitter->name ?? '—' }}</p>
                </div>
            </div>

            <!-- Packages -->
            <div class="mt-4">
                <p class="text-xs font-bold uppercase text-slate-400 mb-2">Assigned Packages</p>
                <div class="flex flex-wrap gap-2">
                    @forelse($beneficiary->packages as $pkg)
                    <span class="px-3 py-1.5 text-xs font-semibold bg-primary/10 text-primary rounded-lg">{{ $pkg->name }} (${{ $pkg->value }})</span>
                    @empty
                    <span class="text-sm text-slate-400">No packages assigned yet.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4">Uploaded Documents</h3>
            @if($beneficiary->documents->count())
            <div class="grid grid-cols-3 gap-3">
                @foreach($beneficiary->documents as $doc)
                <div class="border border-slate-200 rounded-lg p-3 text-center">
                    <span class="material-icons text-slate-400 text-2xl mb-1">description</span>
                    <p class="text-xs font-semibold text-slate-600">{{ $doc->type }}</p>
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-xs text-primary hover:underline">View</a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400">No documents uploaded.</p>
            @endif
        </div>

        <!-- Review History -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4">Review History</h3>
            @forelse($beneficiary->reviews as $review)
            <div class="flex gap-4 mb-4 last:mb-0 pb-4 last:pb-0 border-b last:border-b-0 border-slate-100">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $review->action === 'approve' ? 'bg-emerald-100 text-emerald-600' : ($review->action === 'fraud' ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600') }}">
                    <span class="material-icons text-sm">{{ $review->action === 'approve' ? 'check' : ($review->action === 'fraud' ? 'warning' : 'close') }}</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">{{ ucfirst($review->action) }}d by {{ $review->manager->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $review->created_at->format('M d, Y H:i A') }}</p>
                    @if($review->remarks)
                    <p class="text-sm text-slate-600 mt-1 bg-slate-50 rounded p-2 italic">"{{ $review->remarks }}"</p>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400">No reviews yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Action Panel + Timeline -->
    <div class="space-y-6">
        <!-- Review Action Panel -->
        @if(in_array($beneficiary->status, ['submitted', 'under_review']))
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4">Take Action</h3>
            <form method="POST" action="{{ route('beneficiaries.review', $beneficiary) }}" id="reviewForm">
                @csrf
                <input type="hidden" name="action" id="reviewAction" value=""/>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none" placeholder="Add your review comments..."></textarea>
                    @error('remarks') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <button type="button" onclick="submitReview('approve')" class="w-full flex items-center justify-center gap-2 py-3 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-colors font-semibold text-sm shadow-lg shadow-emerald-500/25">
                        <span class="material-icons text-sm">check_circle</span> Approve Selected
                    </button>
                    <button type="button" onclick="submitReview('reject')" class="w-full flex items-center justify-center gap-2 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition-colors font-semibold text-sm">
                        <span class="material-icons text-sm">cancel</span> Reject
                    </button>
                    <button type="button" onclick="submitReview('fraud')" class="w-full flex items-center justify-center gap-2 py-3 bg-rose-500 text-white rounded-xl hover:bg-rose-600 transition-colors font-semibold text-sm">
                        <span class="material-icons text-sm">warning</span> Mark as Fraud
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Package Approval & Biometric Confirmation -->
        @if($beneficiary->status === 'approved')
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-icons text-primary">inventory_2</span> Package Approval
            </h3>
            
            <form method="POST" action="{{ route('beneficiaries.approve-packages', $beneficiary) }}" id="packageApproveForm">
                @csrf
                <input type="hidden" name="demo_fingerprint" id="demoFingerprintToken" value="0">

                <!-- Package List -->
                <div class="mb-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <p class="text-xs font-bold uppercase text-slate-500 mb-2">Select Packages to Release</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @forelse($beneficiary->packages as $pkg)
                            @php
                                $isProcessed = \App\Models\Transaction::where('beneficiary_id', $beneficiary->id)->where('package_id', $pkg->id)->exists();
                            @endphp
                            <label class="flex items-center gap-3 p-2 rounded hover:bg-white border border-transparent hover:border-slate-200 transition-colors {{ $isProcessed ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                <input type="checkbox" name="approved_package_ids[]" value="{{ $pkg->id }}" 
                                    {{ $isProcessed ? 'disabled' : 'checked' }} 
                                    class="rounded text-emerald-500 focus:ring-emerald-500 border-slate-300 w-4 h-4">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-700">{{ $pkg->name }}</p>
                                    <p class="text-xs text-slate-500">${{ $pkg->value }}</p>
                                </div>
                                @if($isProcessed)
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded">ALREADY PROCESSED</span>
                                @endif
                            </label>
                        @empty
                            <p class="text-sm text-slate-400">No packages assigned.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Biometric Demo + SecuGen -->
                <div class="mb-6 border-t border-slate-100 pt-6">
                    <p class="text-xs font-bold uppercase text-slate-500 mb-3">Biometric Confirmation (Threshold: 80%)</p>
                    
                    <!-- Hidden Stored Template for Matching -->
                    <input type="hidden" id="storedTemplate" value="{{ $beneficiary->biometric_template }}">

                    <div id="biometricStep1" class="text-center py-6 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300">
                        <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                            <span class="material-icons text-slate-400 text-3xl">fingerprint</span>
                        </div>
                        <p class="text-sm text-slate-600 mb-3">Connect SecuGen Scanner & Place Finger</p>
                        
                        <div class="flex flex-col items-center gap-3">
                            <!-- SecuGen Button -->
                            <button type="button" onclick="captureFP()" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                                <span class="material-icons text-sm">usb</span> Scan with Device
                            </button>

                            <p class="text-[10px] text-slate-400 font-medium">- OR USE SIMULATION -</p>

                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="simulateFingerprint(false)" class="px-3 py-1.5 bg-slate-200 text-slate-600 text-[10px] font-bold rounded hover:bg-slate-300 transition-colors">
                                    Demo Pass
                                </button>
                                <button type="button" onclick="simulateFingerprint(true)" class="px-3 py-1.5 bg-slate-200 text-slate-600 text-[10px] font-bold rounded hover:bg-slate-300 transition-colors">
                                    Demo Fail
                                </button>
                                <button type="button" onclick="testMatchService()" class="px-3 py-1.5 bg-yellow-200 text-yellow-800 text-[10px] font-bold rounded hover:bg-yellow-300 transition-colors">
                                    ? Debug Service
                                </button>
                            </div>
                            <div id="statusMessage" class="text-xs font-semibold text-slate-500 h-4 mt-1"></div>
                        </div>
                    </div>

                    <div id="biometricStep2" class="hidden text-center py-6 bg-emerald-50 rounded-xl border border-emerald-200">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="material-icons text-emerald-500 text-3xl">check_circle</span>
                        </div>
                        <p class="text-sm font-bold text-emerald-700 mb-1">Identity Verified!</p>
                        <p class="text-xs text-emerald-600" id="matchScoreDisplay">Match Score: --%</p>
                    </div>
                </div>

                <button type="submit" id="approveBtn" disabled class="w-full py-3 bg-emerald-500 text-white rounded-xl font-bold hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
                    Approve & Generate Transactions
                </button>
            </form>
        </div>
        @endif

        @if(auth()->user()->hasRole('super_admin') || auth()->user()->can('review_beneficiaries'))
        <div class="bg-slate-50 rounded-xl border border-slate-200 shadow-inner p-4 mt-6">
            <h3 class="font-bold text-slate-700 mb-3 text-sm flex items-center gap-2">
                <span class="material-icons text-slate-400 text-base">build</span> Admin Corrections
            </h3>
            <form method="POST" action="{{ route('beneficiaries.update', $beneficiary) }}">
                @csrf
                @method('PUT')
                <div class="flex gap-2">
                    <select name="status" class="flex-1 px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary outline-none">
                        <option value="submitted" {{ $beneficiary->status == 'submitted' ? 'selected' : '' }}>Reset to Submitted</option>
                        <option value="under_review" {{ $beneficiary->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ $beneficiary->status == 'approved' ? 'selected' : '' }}>Force Approve</option>
                        <option value="rejected" {{ $beneficiary->status == 'rejected' ? 'selected' : '' }}>Force Reject</option>
                        <option value="fraud" {{ $beneficiary->status == 'fraud' ? 'selected' : '' }}>Mark Fraud</option>
                    </select>
                    <button type="submit" onclick="return confirm('Are you sure you want to manually change the status? This is for correction purposes.')" class="bg-slate-800 text-white px-3 py-2 rounded-lg text-xs font-semibold hover:bg-slate-700">Update</button>
                </div>
                <input type="text" name="remarks" placeholder="Reason for correction..." class="w-full mt-2 px-3 py-2 text-xs border border-slate-300 rounded-lg outline-none">
            </form>
        </div>
        @endif

        <!-- Activity Timeline -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mt-6">
            <h3 class="font-bold text-slate-800 mb-4">Activity Timeline</h3>
            <div class="relative space-y-6">
                <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-slate-200"></div>

                <!-- Creation -->
                <div class="relative flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center relative z-10 text-white shadow-lg shrink-0">
                        <span class="material-icons text-xs">add</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-700">Record Created</p>
                        <p class="text-xs text-slate-500">{{ $beneficiary->created_at?->format('M d, Y • h:i A') }}</p>
                        <p class="text-xs text-slate-400">by {{ $beneficiary->submitter->name ?? '—' }}</p>
                    </div>
                </div>

                @foreach($timeline as $log)
                <div class="relative flex gap-4">
                    <div class="w-8 h-8 rounded-full {{ $log->action === 'approve' ? 'bg-emerald-500' : ($log->action === 'fraud' ? 'bg-rose-500' : 'bg-primary') }} flex items-center justify-center relative z-10 text-white shadow-lg shrink-0">
                        <span class="material-icons text-xs">{{ $log->action === 'approve' ? 'check' : ($log->action === 'fraud' ? 'warning' : 'edit') }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-700">{{ $log->description }}</p>
                        <p class="text-xs text-slate-500">{{ $log->created_at->format('M d, Y • h:i A') }}</p>
                        <p class="text-xs text-slate-400">by {{ $log->user->name ?? 'System' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitReview(action) {
    const form = document.getElementById('reviewForm');
    const remarks = form.querySelector('textarea[name="remarks"]').value;

    if ((action === 'reject' || action === 'fraud') && !remarks.trim()) {
        alert('Remarks are mandatory for rejection/fraud.');
        return;
    }
    if (action === 'fraud' && !confirm('Are you sure you want to mark this as FRAUD? This will lock the beneficiary and flag the volunteer.')) {
        return;
    }
    document.getElementById('reviewAction').value = action;
    form.submit();
}

function simulateFingerprint(forceFail = false) {
    const step1 = document.getElementById('biometricStep1');
    const step2 = document.getElementById('biometricStep2');
    const btn = document.getElementById('approveBtn');
    const token = document.getElementById('demoFingerprintToken');
    const csrf = document.querySelector('#packageApproveForm input[name="_token"]').value;
    const scoreDisplay = document.getElementById('matchScoreDisplay');

    // Show scanning state
    step1.innerHTML = '<div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto"></div><p class="text-xs text-slate-500 mt-2">Checking Database...</p>';

    // Call Real Verification Endpoint
    fetch("{{ route('beneficiaries.verify-biometric', $beneficiary) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
            force_fail: forceFail
            // In real world, we would send captured data here
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
            btn.disabled = false;
            token.value = "1"; // Set success token
            scoreDisplay.textContent = "Match Score: " + data.match_score + "% (Passed)";
        } else {
            alert("Verification Failed: " + data.message + (data.match_score ? " (Score: " + data.match_score + "%)" : ""));
            // Reset UI with BOTH buttons
            step1.innerHTML = `
                <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                    <span class="material-icons text-slate-400 text-3xl">fingerprint</span>
                </div>
                <p class="text-sm text-slate-600 mb-3">Connect SecuGen Scanner & Place Finger</p>
                        
                <div class="flex flex-col items-center gap-3">
                    <!-- SecuGen Button -->
                    <button type="button" onclick="captureFP()" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                        <span class="material-icons text-sm">usb</span> Scan with Device
                    </button>

                    <p class="text-[10px] text-slate-400 font-medium">- OR USE SIMULATION -</p>

                    <div class="flex justify-center gap-2">
                        <button type="button" onclick="simulateFingerprint(false)" class="px-3 py-1.5 bg-slate-200 text-slate-600 text-[10px] font-bold rounded hover:bg-slate-300 transition-colors">
                            Demo Pass
                        </button>
                        <button type="button" onclick="simulateFingerprint(true)" class="px-3 py-1.5 bg-slate-200 text-slate-600 text-[10px] font-bold rounded hover:bg-slate-300 transition-colors">
                            Demo Fail
                        </button>
                    </div>
                    <div id="statusMessage" class="text-xs font-semibold text-slate-500 h-4 mt-1"></div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("System Error during verification.");
        // Reset UI with BOTH buttons (Duplicate logic, ideally refactor but keeping inline for simplicity in tool usage)
        step1.innerHTML = `
                 <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                    <span class="material-icons text-slate-400 text-3xl">fingerprint</span>
                </div>
                <p class="text-sm text-slate-600 mb-3">Connect SecuGen Scanner & Place Finger</p>
                        
                <div class="flex flex-col items-center gap-3">
                    <!-- SecuGen Button -->
                    <button type="button" onclick="captureFP()" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                        <span class="material-icons text-sm">usb</span> Scan with Device
                    </button>

                    <p class="text-[10px] text-slate-400 font-medium">- OR USE SIMULATION -</p>

                    <div class="flex justify-center gap-2">
                        <button type="button" onclick="simulateFingerprint(false)" class="px-3 py-1.5 bg-slate-200 text-slate-600 text-[10px] font-bold rounded hover:bg-slate-300 transition-colors">
                            Demo Pass
                        </button>
                        <button type="button" onclick="simulateFingerprint(true)" class="px-3 py-1.5 bg-slate-200 text-slate-600 text-[10px] font-bold rounded hover:bg-slate-300 transition-colors">
                            Demo Fail
                        </button>
                    </div>
                    <div id="statusMessage" class="text-xs font-semibold text-slate-500 h-4 mt-1"></div>
                </div>
            `;
    });
}
// ================= SECUGEN WEBAPI INTEGRATION =================

// --- 1. CAPTURE (WebAPI 8443) ---
function captureFP() {
    // 1. Check if stored template exists
    var storedTemplate = document.getElementById('storedTemplate').value;
    if (!storedTemplate || storedTemplate === "") {
        alert("Error: No enrolled fingerprint found for this beneficiary!");
        return;
    }

    var statusMsg = document.getElementById('statusMessage');
    statusMsg.innerText = "Scanning via WebAPI (Port 8443)...";
    statusMsg.className = "text-xs font-bold text-blue-600 animate-pulse mt-1";

    var uri = "https://localhost:8443/SGIFPCapture";
    var xmlhttp = new XMLHttpRequest();
    
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var fpobject = JSON.parse(xmlhttp.responseText);
            if (fpobject.ErrorCode == 0) {
                // Scenario: Capture Successful -> Now Verification
                var capturedTemplate = fpobject.TemplateBase64;
                
                // Integrity Checks
                if (storedTemplate.length > 5000) {
                    statusMsg.innerText = "Error: Stored data is Image!";
                    alert("CRITICAL: Stored data is an Image, not a Template.");
                    return;
                }
                if (storedTemplate.startsWith("MOCK_")) {
                    statusMsg.innerText = "Error: Cannot verify MOCK data.";
                    alert("Cannot verify against MOCK data. Please re-enroll.");
                    return;
                }

                statusMsg.innerText = "Capture Success. Verifying...";
                verifyFP(capturedTemplate, storedTemplate);
            } else {
                statusMsg.innerText = "Scanner Error #" + fpobject.ErrorCode;
                statusMsg.className = "text-xs font-bold text-red-500 mt-1";
                alert("Scanner Error: " + fpobject.ErrorCode);
            }
        } else if (xmlhttp.status == 404) {
             statusMsg.innerText = "WebAPI Service Not Found.";
        }
    }
    
    // Force ISO Format
    var params = "Timeout=10000&Quality=40&licstr=&templateFormat=ISO";
    
    xmlhttp.open("POST", uri, true);
    xmlhttp.timeout = 10000;
    xmlhttp.send(params);

    xmlhttp.onerror = function (e) {
        statusMsg.innerHTML = '<span class="text-amber-600 font-bold">Connection Failed.</span> <a href="https://localhost:8443/SGIFPCapture" target="_blank" class="underline">Check Cert</a>';
    };
}

// --- 2. VERIFY (WebAPI 8443) ---
function verifyFP(capturedTemplate, storedTemplate) {
    console.log("Starting Verification process (WebAPI)...");
    var statusMsg = document.getElementById('statusMessage');
    
    var uri = "https://localhost:8443/SGIFPMatch";
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var fpobject = JSON.parse(xmlhttp.responseText);
                if (fpobject.ErrorCode == 0) {
                    var score = fpobject.MatchingScore;
                    if (score >= 80) {
                        // SUCCESS: Log to backend
                        logVerificationSuccess();
                        handleSuccess(score);
                         statusMsg.innerText = "Matched! Score: " + score;
                         statusMsg.className = "text-xs font-bold text-emerald-600 mt-1";
                    } else {
                        handleFailure(score);
                    }
                } else {
                    alert("Matching Error: " + fpobject.ErrorCode);
                    statusMsg.innerText = "Match Error #" + fpobject.ErrorCode;
                }
            } else {
                statusMsg.innerText = "Match Service Error: " + xmlhttp.status;
            }
        }
    };

    // Both templates must be ISO
    var params = "template1=" + encodeURIComponent(capturedTemplate) + 
                 "&template2=" + encodeURIComponent(storedTemplate) + 
                 "&templateFormat=ISO" + 
                 "&licstr=";
    
    xmlhttp.open("POST", uri, true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.timeout = 10000;
    xmlhttp.send(params);
}

// --- 3. LOG SUCCESS TO BACKEND ---
function logVerificationSuccess() {
    const beneficiaryId = "{{ $beneficiary->id }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    fetch(`/api/v1/beneficiary/${beneficiaryId}/verify-biometric`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Authorization': 'Bearer ' + '{{ auth()->user()->createToken("temp_verify")->plainTextToken }}'
        }
    }); // We don't wait for this, just fire and forget
}

function testMatchService() {
    var uri = "https://localhost:8443/SGIFPMatch";
    var xmlhttp = new XMLHttpRequest();
    var statusMsg = document.getElementById('statusMessage');
    
    statusMsg.innerText = "Testing Match Service access...";
    statusMsg.className = "text-xs font-bold text-blue-500 mt-1";

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var fpobject = JSON.parse(xmlhttp.responseText);
                alert("Match Service is WORKING! \nResponse: " + JSON.stringify(fpobject));
                statusMsg.innerText = "Service OK. Error Code: " + fpobject.ErrorCode;
                statusMsg.className = "text-xs font-bold text-emerald-600 mt-1";
            } else {
                alert("Match Service Failed (Status " + xmlhttp.status + ").\n\nPossible Causes:\n1. Cert not accepted (Check 10004 link)\n2. Antivirus blocking port 8443\n3. Service crashed");
                statusMsg.innerText = "Service Check Failed: " + xmlhttp.status;
                statusMsg.className = "text-xs font-bold text-red-600 mt-1";
            }
        }
    };
    
    // Test with dummy data - should return Error, but NOT Status 0
    var params = "template1=TEST&template2=TEST&templateFormat=ISO&licstr=";
    xmlhttp.open("POST", uri, true);
    xmlhttp.timeout = 5000;
    xmlhttp.send(params);
}

function logClientError(msg, data) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    fetch('{{ route("client.log") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ message: msg, ...data })
    }).catch(e => console.error("Logging failed", e));
}

function handleSuccess(score) {
    const step1 = document.getElementById('biometricStep1');
    const step2 = document.getElementById('biometricStep2');
    const btn = document.getElementById('approveBtn');
    const token = document.getElementById('demoFingerprintToken');
    const scoreDisplay = document.getElementById('matchScoreDisplay');

    step1.classList.add('hidden');
    step2.classList.remove('hidden');
    btn.disabled = false;
    token.value = "1";
    scoreDisplay.textContent = "Match Score: " + score + "% (Device Verified)";
}

function handleFailure(score) {
    var statusMsg = document.getElementById('statusMessage');
    statusMsg.innerText = "Match Failed! Score: " + score + "% (Req: 80%)";
    statusMsg.className = "text-xs font-bold text-rose-600 mt-1";
    alert("Identity Mismatch! Score: " + score + "%");
}
</script>
@endpush
@endsection
