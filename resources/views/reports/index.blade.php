@extends('layouts.app')
@section('title', 'Reports')
@section('header', 'Reports & Analytics')
@section('subheader', 'Comprehensive data visualization and export')

@section('content')
<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    @php
        $kpis = [
            ['label'=>'Total','value'=>$total,'icon'=>'groups','color'=>'bg-primary text-white','growColor'=>'bg-primary/10'],
            ['label'=>'Approved','value'=>$approved,'icon'=>'check_circle','color'=>'bg-emerald-500 text-white','growColor'=>'bg-emerald-50'],
            ['label'=>'Pending','value'=>$pending,'icon'=>'schedule','color'=>'bg-amber-500 text-white','growColor'=>'bg-amber-50'],
            ['label'=>'Rejected','value'=>$rejected,'icon'=>'cancel','color'=>'bg-red-500 text-white','growColor'=>'bg-red-50'],
            ['label'=>'Fraud','value'=>$fraud,'icon'=>'warning','color'=>'bg-rose-600 text-white','growColor'=>'bg-rose-50'],
        ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg {{ $kpi['color'] }} flex items-center justify-center shadow-lg">
                <span class="material-icons text-lg">{{ $kpi['icon'] }}</span>
            </div>
            <p class="text-xs font-semibold uppercase text-slate-400">{{ $kpi['label'] }}</p>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($kpi['value']) }}</p>
        @if($total > 0)
        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full {{ str_replace('bg-white', '', $kpi['color']) }} rounded-full" style="width: {{ round($kpi['value'] / max($total, 1) * 100) }}%"></div>
        </div>
        @endif
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Organization</label>
            <select name="organization_id" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">All Organizations</option>
                @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Program</label>
            <select name="program_id" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">All Programs</option>
                @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
        </div>
        <button type="submit" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">Apply</button>
        <a href="{{ route('reports.export.csv') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold border border-emerald-500 text-emerald-600 hover:bg-emerald-50 transition-colors flex items-center gap-1">
            <span class="material-icons text-sm">download</span> Export CSV
        </a>
    </form>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Status Distribution (Donut Chart) -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Status Distribution</h3>
        <div class="flex items-center justify-center h-64">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Beneficiaries per Project (Bar Chart) -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Beneficiaries by Project</h3>
        <div class="h-64">
            <canvas id="projectChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Package Distribution -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Package Type Distribution</h3>
        <div class="flex items-center justify-center h-64">
            <canvas id="packageChart"></canvas>
        </div>
    </div>

    <!-- Top Volunteers -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Top Performing Volunteers</h3>
        <div class="space-y-3">
            @forelse($volunteerPerf as $i => $v)
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">{{ $i+1 }}</span>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-semibold text-slate-700">{{ $v->name }}</p>
                        <span class="text-xs font-bold text-primary">{{ $v->submitted_beneficiaries_count }}</span>
                    </div>
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $volunteerPerf->max('submitted_beneficiaries_count') > 0 ? round($v->submitted_beneficiaries_count / $volunteerPerf->max('submitted_beneficiaries_count') * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400">No volunteer submissions yet.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
// Status Donut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending', 'Rejected', 'Fraud'],
        datasets: [{
            data: [{{ $approved }}, {{ $pending }}, {{ $rejected }}, {{ $fraud }}],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#e11d48'],
            borderWidth: 0,
            hoverOffset: 8,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' } },
        },
    },
});

// Project Bar Chart
new Chart(document.getElementById('projectChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($perProject->pluck('name')) !!},
        datasets: [{
            label: 'Beneficiaries',
            data: {!! json_encode($perProject->pluck('count')) !!},
            backgroundColor: '#1e1e8a',
            borderRadius: 8,
            barThickness: 32,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } },
        },
    },
});

// Package Donut
new Chart(document.getElementById('packageChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($packageDist->pluck('type')->map(fn($t) => ucfirst($t))) !!},
        datasets: [{
            data: {!! json_encode($packageDist->pluck('count')) !!},
            backgroundColor: ['#10b981', '#f97316', '#e11d48', '#3b82f6', '#8b5cf6'],
            borderWidth: 0,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' } },
        },
    },
});
</script>
@endpush
@endsection
