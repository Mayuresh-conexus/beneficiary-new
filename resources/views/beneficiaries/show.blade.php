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
                        <span class="material-icons text-sm">check_circle</span> Approve
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

        <!-- Activity Timeline -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
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
</script>
@endpush
@endsection
