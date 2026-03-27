<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\BeneficiaryController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleManagementController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class , 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class , 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class , 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

    // Organizations
    Route::resource('organizations', OrganizationController::class)->except(['show'])->middleware('permission:view_organizations');

    // User Management
    Route::resource('users', UserController::class)->middleware('permission:view_users');

    // Roles & Permissions
    Route::resource('roles', RoleManagementController::class)->middleware('permission:view_roles');

    // Programs
    Route::resource('programs', ProgramController::class)->except(['show'])->middleware('permission:view_projects');

    // Packages
    Route::resource('packages', PackageController::class)->except(['show'])->middleware('permission:view_projects');

    // Projects
    Route::resource('projects', ProjectController::class)->middleware('permission:view_projects');

    // AJAX helpers for dependent dropdowns
    Route::get('/ajax/programs-by-org/{orgId}', [ProjectController::class , 'programsByOrganization'])->name('ajax.programs');
    Route::get('/ajax/packages-by-program/{programId}', [ProjectController::class , 'packagesByProgram'])->name('ajax.packages');
    Route::get('/ajax/packages-by-project/{projectId}', [ProjectController::class , 'packagesByProject'])->name('ajax.project-packages');

    // Beneficiaries
    Route::get('/beneficiaries', [BeneficiaryController::class , 'index'])->name('beneficiaries.index')->middleware('permission:view_beneficiaries');
    Route::get('/beneficiaries/create', [BeneficiaryController::class , 'create'])->name('beneficiaries.create')->middleware('permission:create_beneficiaries');
    Route::post('/beneficiaries', [BeneficiaryController::class , 'store'])->name('beneficiaries.store')->middleware('permission:create_beneficiaries');
    Route::get('/beneficiaries/{beneficiary}', [BeneficiaryController::class , 'show'])->name('beneficiaries.show')->middleware('permission:view_beneficiaries');
    Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class , 'update'])->name('beneficiaries.update')->middleware('permission:create_beneficiaries'); // Re-using permission or create new one
    Route::post('/beneficiaries/{beneficiary}/review', [BeneficiaryController::class , 'review'])->name('beneficiaries.review')->middleware('permission:review_beneficiaries');
    Route::post('/beneficiaries/{beneficiary}/approve-packages', [BeneficiaryController::class , 'approvePackages'])->name('beneficiaries.approve-packages')->middleware('permission:review_beneficiaries');
    Route::post('/beneficiaries/{beneficiary}/verify-biometric', [BeneficiaryController::class , 'verifyBiometric'])->name('beneficiaries.verify-biometric');
    Route::post('/client-log', [BeneficiaryController::class , 'clientLog'])->name('client.log');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class , 'index'])->name('audit.index');

    // Reports
    Route::get('/reports', [ReportController::class , 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class , 'exportCsv'])->name('reports.export.csv');

    // Notifications
    Route::get('/notifications', [NotificationController::class , 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class , 'latest'])->name('notifications.latest');
    Route::post('/notifications/{notification}/read', [NotificationController::class , 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class , 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{notification}', [NotificationController::class , 'destroy'])->name('notifications.destroy');

    // Admin AI Chat
    Route::post('/admin/chat', [\App\Http\Controllers\Api\AdminChatController::class , 'sendMessage'])->name('admin.chat');
});
