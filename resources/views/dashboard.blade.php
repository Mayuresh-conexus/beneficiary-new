@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Good ' . (now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening')) . ', ' . (Auth::user()->name ?? 'Admin'))
@section('subheader', 'Here\'s your impact overview for ' . now()->format('F d, Y'))

@section('content')

<!-- KPI Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['value'=>$totalBeneficiaries, 'label'=>'Total Beneficiaries', 'icon'=>'groups', 'color'=>'from-[#1e1e8a] to-[#4040b8]', 'textColor'=>'text-white'],
            ['value'=>$approved, 'label'=>'Approved', 'icon'=>'check_circle', 'color'=>'from-emerald-500 to-emerald-600', 'textColor'=>'text-white'],
            ['value'=>$pending, 'label'=>'Pending Review', 'icon'=>'schedule', 'color'=>'from-amber-400 to-orange-500', 'textColor'=>'text-white'],
            ['value'=>$fraud, 'label'=>'Fraud Flagged', 'icon'=>'warning', 'color'=>'from-rose-500 to-rose-600', 'textColor'=>'text-white'],
        ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-gradient-to-br {{ $card['color'] }} rounded-2xl p-6 {{ $card['textColor'] }} shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <span class="material-icons text-xl">{{ $card['icon'] }}</span>
            </div>
        </div>
        <p class="text-3xl font-bold">{{ number_format($card['value']) }}</p>
        <p class="text-sm opacity-80 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

<!-- Mini Stats -->
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center">
            <span class="material-icons text-violet-500">corporate_fare</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalOrgs }}</p>
            <p class="text-xs font-semibold text-slate-400">Organizations</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-sky-50 rounded-xl flex items-center justify-center">
            <span class="material-icons text-sky-500">assignment</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $activeProjects }}</p>
            <p class="text-xs font-semibold text-slate-400">Active Projects</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center">
            <span class="material-icons text-teal-500">volunteer_activism</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalVolunteers }}</p>
            <p class="text-xs font-semibold text-slate-400">Active Volunteers</p>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Monthly Submissions Trend -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Monthly Submissions</h3>
        <div class="h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Status Donut -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Review Pipeline</h3>
        <div class="flex items-center justify-center h-64">
            <canvas id="statusDonut"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity + Recent Submissions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Activity -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Recent Activity</h3>
        <div class="space-y-4">
            @forelse($recentActivity as $log)
            <div class="flex gap-3 pb-3 border-b border-slate-50 last:border-0">
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-icons text-primary text-xs">{{ $log->action === 'approve' ? 'check' : ($log->action === 'fraud' ? 'warning' : 'edit') }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">{{ Str::limit($log->description, 50) }}</p>
                    <p class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }} by {{ $log->user->name ?? 'System' }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400">No recent activity.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Recent Submissions</h3>
            <a href="{{ route('beneficiaries.index') }}" class="text-primary text-xs font-bold hover:underline">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Beneficiary</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Project</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @php $sc = ['approved'=>'bg-emerald-100 text-emerald-700','fraud'=>'bg-rose-100 text-rose-700','submitted'=>'bg-amber-100 text-amber-700','under_review'=>'bg-sky-100 text-sky-700','rejected'=>'bg-red-100 text-red-700','draft'=>'bg-slate-100 text-slate-500']; @endphp
                @forelse($recentSubmissions as $b)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 text-sm font-semibold text-slate-700">{{ $b->first_name }} {{ $b->last_name }}</td>
                        <td class="px-6 py-3 text-sm text-slate-500">{{ $b->project->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $sc[$b->status] ?? 'bg-slate-100' }}">{{ str_replace('_', ' ', $b->status) }}</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-400">{{ $b->created_at?->diffForHumans() }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('beneficiaries.show', $b) }}" class="text-primary text-xs font-bold hover:underline">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-slate-400">No submissions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Monthly Submissions Area Chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyData->pluck('label')) !!},
        datasets: [{
            label: 'Submissions',
            data: {!! json_encode($monthlyData->pluck('count')) !!},
            borderColor: '#1e1e8a',
            backgroundColor: 'rgba(30, 30, 138, 0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#1e1e8a',
            borderWidth: 2,
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

// Status Donut
new Chart(document.getElementById('statusDonut'), {
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
</script>
@endpush
@endsection
