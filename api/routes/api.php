<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Tasks\ProjectController;
use App\Http\Controllers\Api\Tasks\TaskController;
use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // User profile
    Route::get('/me', [ProfileController::class, 'show']);
    Route::put('/me', [ProfileController::class, 'update']);
    Route::put('/me/password', [ProfileController::class, 'updatePassword']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Tenant management
    Route::get('/tenant', [TenantController::class, 'show']);
    Route::put('/tenant', [TenantController::class, 'update']);

    // Modules
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{module}', [ModuleController::class, 'show']);

    // Tasks Module
    Route::middleware(['feature:tasks'])->prefix('tasks')->name('tasks.')->group(function () {
        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
        Route::patch('tasks/{task}/assign', [TaskController::class, 'assign']);
    });

    // CRM Module
    Route::middleware(['feature:crm'])->prefix('crm')->name('crm.')->group(function () {
        // CRM routes will be added here
    });

    // Invoicing Module
    Route::middleware(['feature:invoicing'])->prefix('invoicing')->name('invoicing.')->group(function () {
        // Invoicing routes will be added here
    });

    // Marketing Module
    Route::middleware(['feature:marketing'])->prefix('marketing')->name('marketing.')->group(function () {
        // Marketing routes will be added here
    });

    // Automation Module
    Route::middleware(['feature:automation'])->prefix('automation')->name('automation.')->group(function () {
        // Automation routes will be added here
    });

    // Analytics Module
    Route::middleware(['feature:analytics'])->prefix('analytics')->name('analytics.')->group(function () {
        // Analytics routes will be added here
    });

    // Docs Module
    Route::middleware(['feature:docs'])->prefix('docs')->name('docs.')->group(function () {
        // Docs routes will be added here
    });

    // Helpdesk Module
    Route::middleware(['feature:helpdesk'])->prefix('helpdesk')->name('helpdesk.')->group(function () {
        // Helpdesk routes will be added here
    });

    // Admin - User Management & Content Library
    Route::middleware(['role:owner|admin'])->prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::get('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::put('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update']);

        // Content PDFs
        Route::get('content/pdfs', [\App\Http\Controllers\Admin\ContentPdfController::class, 'index']);
        Route::post('content/pdfs', [\App\Http\Controllers\Admin\ContentPdfController::class, 'store']);
        Route::get('content/pdfs/{id}', [\App\Http\Controllers\Admin\ContentPdfController::class, 'show']);
        Route::get('content/pdfs/{id}/download', [\App\Http\Controllers\Admin\ContentPdfController::class, 'download']);
        Route::patch('content/pdfs/{id}/pages/{pageId}/toggle', [\App\Http\Controllers\Admin\ContentPdfController::class, 'togglePage']);
        Route::patch('content/pdfs/{id}/images/{imageId}/toggle', [\App\Http\Controllers\Admin\ContentPdfController::class, 'toggleImage']);
    });
});
