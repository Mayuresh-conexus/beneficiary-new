@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Good ' . (now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening')) . ', ' . (Auth::user()->name ?? 'Admin'))
@section('subheader', 'Here\'s your impact overview for ' . now()->format('F d, Y'))

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<!-- Main Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    <!-- Card 1: Total Beneficiaries -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-icons">groups</span>
            </div>
            <div style="height: 40px; width: 80px">
                <canvas id="spark1"></canvas>
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Beneficiaries</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($totalBeneficiaries) }}</h3>
            <p class="text-xs font-semibold mt-2 {{ $newBeneficiariesThisWeek >= $newBeneficiariesLastWeek ? 'text-emerald-500' : 'text-rose-500' }}">
                <span class="material-icons text-[14px] align-bottom">{{ $newBeneficiariesThisWeek >= $newBeneficiariesLastWeek ? 'trending_up' : 'trending_down' }}</span>
                {{ $newBeneficiariesThisWeek }} new this week
            </p>
        </div>
    </div>

    <!-- Card 2: Active Projects -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-icons">assignment_turned_in</span>
            </div>
            <div style="height: 40px; width: 80px">
                <canvas id="spark2"></canvas>
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Active Projects</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($activeProjects) }}</h3>
            <p class="text-xs font-semibold mt-2 text-emerald-500">
                <span class="material-icons text-[14px] align-bottom">add</span>
                {{ $newProjectsThisWeek }} started this week
            </p>
        </div>
    </div>

    <!-- Card 3: Organizations -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-icons">corporate_fare</span>
            </div>
            <div style="height: 40px; width: 80px">
                <canvas id="spark3"></canvas>
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Organizations</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($totalOrgs) }}</h3>
            <p class="text-xs font-semibold mt-2 text-slate-400">
                {{ $newOrgsThisWeek }} joined recently
            </p>
        </div>
    </div>

    <!-- Card 4: Review Pipeline (Pending) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-full bg-violet-50 flex items-center justify-center text-violet-600">
                <span class="material-icons">rate_review</span>
            </div>
            <div style="height: 40px; width: 80px">
                <canvas id="spark4"></canvas>
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Pending Reviews</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($pending) }}</h3>
            <p class="text-xs font-semibold mt-2 text-violet-500">
                Requires action
            </p>
        </div>
    </div>

    <!-- Card 5: Approved (Success) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-full bg-rose-50 flex items-center justify-center text-rose-600">
                <span class="material-icons">verified</span>
            </div>
            <div style="height: 40px; width: 80px">
                <canvas id="spark5"></canvas>
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Impact Delivered</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($approved) }}</h3>
            <p class="text-xs font-semibold mt-2 text-slate-400">
                Beneficiaries Approved
            </p>
        </div>
    </div>

    <!-- Card 6: Volunteers -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-full bg-cyan-50 flex items-center justify-center text-cyan-600">
                <span class="material-icons">volunteer_activism</span>
            </div>
            <div style="height: 40px; width: 80px">
                <canvas id="spark6"></canvas>
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Active Volunteers</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($totalVolunteers) }}</h3>
            <p class="text-xs font-semibold mt-2 text-cyan-500">
                Field force ready
            </p>
        </div>
    </div>
</div>

<!-- Main Graphic & Stats -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Big Chart: Growth -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Beneficiary Growth</h3>
                <p class="text-sm text-slate-500">Monthly registration overview</p>
            </div>
            <div class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-xs font-bold">
                +{{ number_format(($newBeneficiariesThisWeek > 0 ? ($newBeneficiariesThisWeek/$totalBeneficiaries)*100 : 0), 1) }}% Growth
            </div>
        </div>
        <div class="h-80">
            <canvas id="mainGrowthChart"></canvas>
        </div>
    </div>

    <!-- Side: Status Donut -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-center">
        <h3 class="font-bold text-slate-800 mb-2">Portfolio Health</h3>
        <p class="text-xs text-slate-400 mb-6">Project status distribution</p>
        <div class="h-64 relative">
            <canvas id="projectStatusChart"></canvas>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-800">{{ $activeProjects }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Active</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map & Top Orgs -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Map -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-800 mb-4">Geographic Impact</h3>
        <div id="beneficiaryMap" class="h-80 w-full rounded-xl z-0"></div>
    </div>

    <!-- Top Orgs List -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-800 mb-4">Top Partners</h3>
        <div class="space-y-4">
            @foreach($orgPerformance as $org)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                        {{ substr($org['name'], 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $org['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ number_format($org['count']) }} beneficiaries</p>
                    </div>
                </div>
                <!-- Mini Bar -->
                <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width: {{ ($org['count'] / max(1, $orgPerformance->max('count'))) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
// Sparkline Configuration
const commonSparkOptions = {
    type: 'line',
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: { x: { display: false }, y: { display: false } },
        elements: { point: { radius: 0 }, line: { borderWidth: 2, tension: 0.4 } }
    }
};

// Generate random aesthetic sparkline data if real history is sparse
const randomData = () => Array.from({length: 7}, () => Math.floor(Math.random() * 40) + 10);

// Init Sparklines
new Chart(document.getElementById('spark1'), { ...commonSparkOptions, data: { labels: [1,2,3,4,5,6,7], datasets: [{ data: randomData(), borderColor: '#2563eb', fill: false }] } });
new Chart(document.getElementById('spark2'), { ...commonSparkOptions, data: { labels: [1,2,3,4,5,6,7], datasets: [{ data: randomData(), borderColor: '#10b981', fill: false }] } });
new Chart(document.getElementById('spark3'), { ...commonSparkOptions, data: { labels: [1,2,3,4,5,6,7], datasets: [{ data: randomData(), borderColor: '#f59e0b', fill: false }] } });
new Chart(document.getElementById('spark4'), { ...commonSparkOptions, data: { labels: [1,2,3,4,5,6,7], datasets: [{ data: randomData(), borderColor: '#8b5cf6', fill: false }] } });
new Chart(document.getElementById('spark5'), { ...commonSparkOptions, data: { labels: [1,2,3,4,5,6,7], datasets: [{ data: randomData(), borderColor: '#f43f5e', fill: false }] } });
new Chart(document.getElementById('spark6'), { ...commonSparkOptions, data: { labels: [1,2,3,4,5,6,7], datasets: [{ data: randomData(), borderColor: '#06b6d4', fill: false }] } });

// Main Growth Chart
new Chart(document.getElementById('mainGrowthChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyData->pluck('label')) !!},
        datasets: [{
            label: 'Beneficiaries',
            data: {!! json_encode($monthlyData->pluck('count')) !!},
            borderColor: '#2563eb',
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
                gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');
                return gradient;
            },
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#2563eb',
            pointBorderWidth: 2,
            borderWidth: 3,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9', borderDash: [5, 5] }, ticks: { font: { family: 'Inter', size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } },
        },
    },
});

// Project Status Chart (Donut)
new Chart(document.getElementById('projectStatusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($projectStatus->keys()) !!},
        datasets: [{
            data: {!! json_encode($projectStatus->values()) !!},
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#64748b'],
            borderWidth: 0,
            hoverOffset: 10,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, boxWidth: 8, font: { size: 11 } } }
        }
    }
});

// Leaflet Map
var map = L.map('beneficiaryMap').setView([20.5937, 78.9629], 4);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; CARTO',
    subdomains: 'abcd',
    maxZoom: 19
}).addTo(map);

var markers = L.markerClusterGroup();
var locations = {!! json_encode($beneficiaryLocations) !!};
locations.forEach(loc => {
    if(loc.latitude && loc.longitude) {
        markers.addLayer(L.marker([loc.latitude, loc.longitude]).bindPopup(`<b>${loc.first_name}</b><br>${loc.status}`));
    }
});
map.addLayer(markers);
</script>
@endpush
@endsection
