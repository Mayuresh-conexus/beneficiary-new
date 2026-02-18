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
        $newBeneficiariesThisWeek = Beneficiary::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $newBeneficiariesLastWeek = Beneficiary::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();

        $approved = Beneficiary::where('status', 'approved')->count();
        $pending = Beneficiary::whereIn('status', ['submitted', 'under_review'])->count();
        $fraud = Beneficiary::where('status', 'fraud')->count();
        $rejected = Beneficiary::where('status', 'rejected')->count();

        $totalOrgs = Organization::count();
        $newOrgsThisWeek = Organization::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $activeProjects = Project::where('status', 'active')->count();
        $newProjectsThisWeek = Project::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

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

        // Map Data: Beneficiary Locations
        $beneficiaryLocations = Beneficiary::select(['id', 'first_name', 'last_name', 'latitude', 'longitude', 'status'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get();

        // Chart Data: Organization Performance (Top 5 by beneficiary count)
        $orgPerformance = Organization::withCount('beneficiaries')
            ->orderByDesc('beneficiaries_count')
            ->take(5)
            ->get()
            ->map(function ($org) {
            return ['name' => $org->name, 'count' => $org->beneficiaries_count];
        });

        // Chart Data: Project Status Distribution
        $projectStatus = Project::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('dashboard', compact(
            'totalBeneficiaries', 'newBeneficiariesThisWeek', 'newBeneficiariesLastWeek',
            'approved', 'pending', 'fraud', 'rejected',
            'totalOrgs', 'newOrgsThisWeek',
            'activeProjects', 'newProjectsThisWeek', 'totalVolunteers',
            'recentSubmissions', 'recentActivity', 'monthlyData',
            'beneficiaryLocations', 'orgPerformance', 'projectStatus'
        ));
    }
}
