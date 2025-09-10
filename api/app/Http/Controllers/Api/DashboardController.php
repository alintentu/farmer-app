<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'initials' => $user->initials,
                'roles' => $user->roles->pluck('name'),
                'last_login_at' => $user->last_login_at,
            ],
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'plan' => $tenant->plan,
                'is_active' => $tenant->is_active,
                'trial_ends_at' => $tenant->trial_ends_at,
                'subscription_ends_at' => $tenant->subscription_ends_at,
                'is_on_trial' => $tenant->isOnTrial(),
                'has_active_subscription' => $tenant->hasActiveSubscription(),
            ],
            'modules' => $this->getActiveModules($tenant),
            'quick_stats' => $this->getQuickStats($tenant),
            'recent_activity' => $this->getRecentActivity($user),
        ];

        return response()->json($data);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        $stats = [
            'users_count' => $tenant->users()->count(),
            'modules_count' => $tenant->modules()->wherePivot('enabled', true)->count(),
            'active_features' => $this->getActiveFeaturesCount($tenant),
            'storage_used' => $this->getStorageUsed($tenant),
            'api_requests_today' => $this->getApiRequestsToday($tenant),
        ];

        return response()->json($stats);
    }

    private function getActiveModules(Tenant $tenant): array
    {
        return $tenant->modules()
            ->wherePivot('enabled', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->map(function ($module) {
                return [
                    'id' => $module->id,
                    'key' => $module->key,
                    'label' => $module->label,
                    'description' => $module->description,
                    'icon' => $module->icon,
                    'limits' => $module->pivot->limits ?? [],
                ];
            })
            ->toArray();
    }

    private function getQuickStats(Tenant $tenant): array
    {
        return [
            'total_users' => $tenant->users()->count(),
            'active_users' => $tenant->users()->where('is_active', true)->count(),
            'total_modules' => $tenant->modules()->wherePivot('enabled', true)->count(),
            'subscription_status' => $tenant->hasActiveSubscription() ? 'active' : 'inactive',
        ];
    }

    private function getRecentActivity(User $user): array
    {
        return $user->activities()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'subject_type' => $activity->subject_type,
                    'subject_id' => $activity->subject_id,
                    'created_at' => $activity->created_at,
                ];
            })
            ->toArray();
    }

    private function getActiveFeaturesCount(Tenant $tenant): int
    {
        return $tenant->modules()
            ->wherePivot('enabled', true)
            ->count();
    }

    private function getStorageUsed(Tenant $tenant): array
    {
        // This would be implemented based on your storage strategy
        return [
            'files' => 0,
            'database' => 0,
            'total' => 0,
        ];
    }

    private function getApiRequestsToday(Tenant $tenant): int
    {
        // This would be implemented with proper API request tracking
        return 0;
    }
}
