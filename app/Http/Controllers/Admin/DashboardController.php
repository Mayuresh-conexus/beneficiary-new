<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBeneficiaries = Beneficiary::count();
        $approved = Beneficiary::where('status', 'approved')->count();
        $pending = Beneficiary::whereIn('status', ['submitted', 'under_review'])->count();
        $fraud = Beneficiary::where('status', 'fraud')->count();
        $rejected = Beneficiary::where('status', 'rejected')->count();
        $totalOrgs = Organization::count();
        $activeProjects = Project::where('status', 'active')->count();
        $totalVolunteers = User::where('role', 'volunteer')->where('is_active', true)->count();

        $recentSubmissions = Beneficiary::with(['project', 'submitter'])
            ->latest()
            ->take(10)
            ->get();

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Chart data: monthly submissions for last 6 months
        $monthlyData = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);
            return [
            'label' => $month->format('M'),
            'count' => Beneficiary::whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->count(),
            ];
        });

        return view('dashboard', compact(
            'totalBeneficiaries', 'approved', 'pending', 'fraud', 'rejected',
            'totalOrgs', 'activeProjects', 'totalVolunteers',
            'recentSubmissions', 'recentActivity', 'monthlyData'
        ));
    }
}
