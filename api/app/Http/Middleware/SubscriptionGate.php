<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SubscriptionGate
{
    /**
     * Service access requirements by plan
     */
    private const SERVICE_ACCESS = [
        'tasks' => ['starter', 'solo', 'team', 'growth', 'scale', 'enterprise'],
        'crm' => ['team', 'growth', 'scale', 'enterprise'],
        'invoicing' => ['starter', 'solo', 'team', 'growth', 'scale', 'enterprise'],
        'marketing' => ['growth', 'scale', 'enterprise'],
        'analytics' => ['scale', 'enterprise'],
        'communication' => ['starter', 'solo', 'team', 'growth', 'scale', 'enterprise'],
        'files' => ['starter', 'solo', 'team', 'growth', 'scale', 'enterprise'],
        'search' => ['starter', 'solo', 'team', 'growth', 'scale', 'enterprise'],
    ];

    /**
     * Service limits by plan
     */
    private const SERVICE_LIMITS = [
        'tasks' => [
            'starter' => ['projects' => 5, 'tasks_per_project' => 100],
            'solo' => ['projects' => 10, 'tasks_per_project' => 200],
            'team' => ['projects' => 25, 'tasks_per_project' => 500],
            'growth' => ['projects' => 100, 'tasks_per_project' => 1000],
            'scale' => ['projects' => 500, 'tasks_per_project' => 5000],
            'enterprise' => ['projects' => -1, 'tasks_per_project' => -1], // unlimited
        ],
        'crm' => [
            'team' => ['contacts' => 100, 'leads' => 50, 'deals' => 25],
            'growth' => ['contacts' => 500, 'leads' => 250, 'deals' => 100],
            'scale' => ['contacts' => 2000, 'leads' => 1000, 'deals' => 500],
            'enterprise' => ['contacts' => -1, 'leads' => -1, 'deals' => -1], // unlimited
        ],
        'invoicing' => [
            'starter' => ['invoices_per_month' => 10, 'expenses_per_month' => 50],
            'solo' => ['invoices_per_month' => 25, 'expenses_per_month' => 100],
            'team' => ['invoices_per_month' => 100, 'expenses_per_month' => 500],
            'growth' => ['invoices_per_month' => 500, 'expenses_per_month' => 2000],
            'scale' => ['invoices_per_month' => 2000, 'expenses_per_month' => 10000],
            'enterprise' => ['invoices_per_month' => -1, 'expenses_per_month' => -1], // unlimited
        ],
        'marketing' => [
            'growth' => ['email_contacts' => 1000, 'campaigns_per_month' => 5],
            'scale' => ['email_contacts' => 10000, 'campaigns_per_month' => 20],
            'enterprise' => ['email_contacts' => -1, 'campaigns_per_month' => -1], // unlimited
        ],
        'files' => [
            'starter' => ['storage_gb' => 1],
            'solo' => ['storage_gb' => 5],
            'team' => ['storage_gb' => 10],
            'growth' => ['storage_gb' => 25],
            'scale' => ['storage_gb' => 100],
            'enterprise' => ['storage_gb' => -1], // unlimited
        ],
    ];

    public function handle(Request $request, Closure $next, string $service): SymfonyResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $tenant = $user->tenant;
        
        if (!$tenant) {
            return response()->json(['message' => 'No tenant associated'], Response::HTTP_FORBIDDEN);
        }

        // Check if service is accessible for the current plan
        if (!$this->canAccessService($service, $tenant->plan)) {
            return response()->json([
                'message' => "Service '{$service}' not available on your plan",
                'service' => $service,
                'plan' => $tenant->plan,
                'upgrade_required' => true,
                'available_plans' => self::SERVICE_ACCESS[$service] ?? []
            ], Response::HTTP_FORBIDDEN);
        }

        // Check subscription status
        if (!$tenant->hasActiveSubscription() && !$tenant->isOnTrial()) {
            return response()->json([
                'message' => 'Subscription expired or inactive',
                'service' => $service,
                'plan' => $tenant->plan,
                'subscription_required' => true
            ], Response::HTTP_FORBIDDEN);
        }

        // Add service limits to request for downstream use
        $request->attributes->set('service_limits', $this->getServiceLimits($service, $tenant->plan));
        $request->attributes->set('current_usage', $this->getCurrentUsage($service, $tenant->id));

        return $next($request);
    }

    /**
     * Check if service is accessible for the given plan
     */
    private function canAccessService(string $service, string $plan): bool
    {
        $allowedPlans = self::SERVICE_ACCESS[$service] ?? [];
        return in_array($plan, $allowedPlans);
    }

    /**
     * Get service limits for the given plan
     */
    private function getServiceLimits(string $service, string $plan): array
    {
        return self::SERVICE_LIMITS[$service][$plan] ?? [];
    }

    /**
     * Get current usage for the service
     */
    private function getCurrentUsage(string $service, string $tenantId): array
    {
        // This would query the specific service's database
        // For now, return empty array - will be implemented per service
        return [];
    }

    /**
     * Check if usage is within limits
     */
    public static function checkUsageLimit(string $service, string $plan, string $resource, int $currentUsage, int $requestedAmount = 1): bool
    {
        $limits = self::SERVICE_LIMITS[$service][$plan] ?? [];
        $limit = $limits[$resource] ?? -1;

        // -1 means unlimited
        if ($limit === -1) {
            return true;
        }

        return ($currentUsage + $requestedAmount) <= $limit;
    }

    /**
     * Get upgrade suggestions for a service
     */
    public static function getUpgradeSuggestions(string $service): array
    {
        $allowedPlans = self::SERVICE_ACCESS[$service] ?? [];
        
        return [
            'service' => $service,
            'available_plans' => $allowedPlans,
            'recommended_plan' => $allowedPlans[0] ?? null,
            'features' => self::getServiceFeatures($service)
        ];
    }

    /**
     * Get service features for marketing
     */
    private static function getServiceFeatures(string $service): array
    {
        $features = [
            'tasks' => [
                'Project management',
                'Task tracking',
                'Kanban boards',
                'Time tracking',
                'Team collaboration'
            ],
            'crm' => [
                'Contact management',
                'Lead management',
                'Deal pipeline',
                'Sales analytics',
                'Email integration'
            ],
            'invoicing' => [
                'Invoice generation',
                'Expense tracking',
                'Payment processing',
                'Financial reporting',
                'Tax calculations'
            ],
            'marketing' => [
                'Email campaigns',
                'Landing pages',
                'Marketing analytics',
                'Lead scoring',
                'A/B testing'
            ],
            'analytics' => [
                'Real-time analytics',
                'Custom reports',
                'Data visualization',
                'Business intelligence',
                'Data export'
            ]
        ];

        return $features[$service] ?? [];
    }
}
