<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =========================================================================
//  LEGACY API ROUTES (backward compatibility)
// =========================================================================
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\NotificationController;

Route::post('/login', [AuthController::class , 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class , 'logout']);
    Route::get('/user', [AuthController::class , 'user']);

    Route::get('/beneficiaries', [BeneficiaryController::class , 'index']);
    Route::post('/beneficiaries', [BeneficiaryController::class , 'store']);
    Route::get('/beneficiaries/{id}', [BeneficiaryController::class , 'show']);
    Route::post('/beneficiaries/{id}/upload', [BeneficiaryController::class , 'uploadDocument']);
    Route::post('/beneficiaries/{id}/review', [BeneficiaryController::class , 'review']);

    Route::get('/notifications', [NotificationController::class , 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class , 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class , 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class , 'markAllAsRead']);

    Route::get('/projects', function (Request $request) {
            return $request->user()->assignedProjects()->with(['program', 'packages'])->get();
        }
        );

        Route::get('/packages', function (Request $request) {
            if ($request->has('project_id')) {
                $project = \App\Models\Project::find($request->project_id);
                return $project ? $project->packages : [];
            }
            return \App\Models\Package::all();
        }
        );

        Route::get('/dashboard/stats', function (Request $request) {
            return response()->json([
            'total_beneficiaries' => \App\Models\Beneficiary::count(),
            'approved' => \App\Models\Beneficiary::where('status', 'approved')->count(),
            'pending' => \App\Models\Beneficiary::whereIn('status', ['submitted', 'under_review'])->count(),
            'fraud' => \App\Models\Beneficiary::where('status', 'fraud')->count(),
            ]);
        }
        );
    });

// =========================================================================
//  V1 API ROUTES — Versioned, structured, production-ready
// =========================================================================
use App\Http\Controllers\Api\V1\AuthController as V1AuthController;
use App\Http\Controllers\Api\V1\BeneficiaryController as V1BeneficiaryController;
use App\Http\Controllers\Api\V1\VolunteerController as V1VolunteerController;

Route::group(['prefix' => 'v1'], function () {

    // --- Auth (Public) ---
    Route::post('/login', [V1AuthController::class , 'login']);

    // --- Protected routes ---
    Route::group(['middleware' => 'auth:sanctum'], function () {

            // Auth
            Route::post('/logout', [V1AuthController::class , 'logout']);
            Route::get('/me', [V1AuthController::class , 'me']);

            // Volunteer-specific
            Route::get('/volunteer/projects', [V1VolunteerController::class , 'projects']);
            Route::get('/volunteer/dashboard', [V1VolunteerController::class , 'dashboard']);
            Route::get('/project/{id}/packages', [V1VolunteerController::class , 'projectPackages']);
            Route::get('/sync-status', [V1VolunteerController::class , 'syncStatus']);

            // Beneficiaries
            Route::post('/beneficiaries', [V1BeneficiaryController::class , 'store']);
            Route::get('/beneficiaries/my-submissions', [V1BeneficiaryController::class , 'mySubmissions']);
            Route::get('/beneficiary/{id}', [V1BeneficiaryController::class , 'show']);
            Route::put('/beneficiary/{id}', [V1BeneficiaryController::class , 'update']);
            Route::post('/beneficiary/{id}/review', [V1BeneficiaryController::class , 'review']);

            // Biometrics
            Route::post('/biometrics/parse-xml', [App\Http\Controllers\Api\V1\BiometricController::class , 'parsePidXml']);
            Route::post('/biometrics/verify', [App\Http\Controllers\Api\V1\BiometricController::class , 'verifyBiometric']);
            Route::get('/beneficiary/{id}/biometric', [V1BeneficiaryController::class , 'biometricData']);
            Route::post('/beneficiary/{id}/verify-biometric', [V1BeneficiaryController::class , 'logVerification']);

            // Transactions (Distribution)
            Route::get('/transactions', [\App\Http\Controllers\Api\V1\TransactionController::class , 'index']);
            Route::post('/transactions/{id}/deliver', [\App\Http\Controllers\Api\V1\TransactionController::class , 'deliver']);

            // Upload
            Route::post('/upload', [V1BeneficiaryController::class , 'upload']);

            // Notifications
            Route::get('/notifications', [NotificationController::class , 'index']);
            Route::get('/notifications/unread-count', [NotificationController::class , 'unreadCount']);
            Route::post('/notifications/{id}/read', [NotificationController::class , 'markAsRead']);
            Route::post('/notifications/mark-all-read', [NotificationController::class , 'markAllAsRead']);

            // Admin - User & Role Management
            Route::group(['prefix' => 'admin', 'middleware' => 'role:super_admin,organization_admin', 'as' => 'api.admin.'], function () {
                    Route::apiResource('users', \App\Http\Controllers\Api\V1\Admin\UserController::class);
                    Route::get('roles', [\App\Http\Controllers\Api\V1\Admin\RoleController::class , 'index'])->name('roles');
                }
                );
            }
            );
        });
