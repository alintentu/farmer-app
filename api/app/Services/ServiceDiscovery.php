<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceDiscovery
{
    private const SERVICE_ENDPOINTS = [
        'core' => 'http://atlas-core.atlas.svc.cluster.local:8000',
        'tasks' => 'http://atlas-tasks.atlas.svc.cluster.local:8000',
        'crm' => 'http://atlas-crm.atlas.svc.cluster.local:8000',
        'invoicing' => 'http://atlas-invoicing.atlas.svc.cluster.local:8000',
        'marketing' => 'http://atlas-marketing.atlas.svc.cluster.local:8000',
        'analytics' => 'http://atlas-analytics.atlas.svc.cluster.local:8000',
        'communication' => 'http://atlas-communication.atlas.svc.cluster.local:8000',
        'files' => 'http://atlas-files.atlas.svc.cluster.local:8000',
        'search' => 'http://atlas-search.atlas.svc.cluster.local:8000',
    ];

    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Get service endpoint
     */
    public static function getServiceEndpoint(string $service): string
    {
        return self::SERVICE_ENDPOINTS[$service] ?? '';
    }

    /**
     * Check if service is healthy
     */
    public static function isServiceHealthy(string $service): bool
    {
        $cacheKey = "service_health_{$service}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($service) {
            try {
                $endpoint = self::getServiceEndpoint($service);
                if (empty($endpoint)) {
                    return false;
                }

                $response = Http::timeout(5)->get("{$endpoint}/health");

                return $response->successful();
            } catch (\Exception $e) {
                Log::warning("Service health check failed for {$service}: ".$e->getMessage());

                return false;
            }
        });
    }

    /**
     * Get all healthy services
     */
    public static function getHealthyServices(): array
    {
        $healthyServices = [];

        foreach (array_keys(self::SERVICE_ENDPOINTS) as $service) {
            if (self::isServiceHealthy($service)) {
                $healthyServices[] = $service;
            }
        }

        return $healthyServices;
    }

    /**
     * Make service-to-service request
     */
    public static function serviceRequest(string $service, string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $baseUrl = self::getServiceEndpoint($service);

        if (empty($baseUrl)) {
            throw new \Exception("Service '{$service}' not found");
        }

        if (! self::isServiceHealthy($service)) {
            throw new \Exception("Service '{$service}' is not healthy");
        }

        try {
            $url = rtrim($baseUrl, '/').'/'.ltrim($endpoint, '/');

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->$method($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("Service request failed for {$service}: ".$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Get service metrics
     */
    public static function getServiceMetrics(string $service): array
    {
        return self::serviceRequest($service, 'get', '/metrics');
    }

    /**
     * Get service configuration
     */
    public static function getServiceConfig(string $service): array
    {
        return self::serviceRequest($service, 'get', '/config');
    }

    /**
     * Broadcast event to all services
     */
    public static function broadcastEvent(string $event, array $payload): array
    {
        $results = [];

        foreach (array_keys(self::SERVICE_ENDPOINTS) as $service) {
            if (self::isServiceHealthy($service)) {
                $results[$service] = self::serviceRequest($service, 'post', '/events', [
                    'event' => $event,
                    'payload' => $payload,
                    'timestamp' => now()->toISOString(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Get service dependencies
     */
    public static function getServiceDependencies(string $service): array
    {
        $dependencies = [
            'core' => [],
            'tasks' => ['core'],
            'crm' => ['core'],
            'invoicing' => ['core'],
            'marketing' => ['core', 'crm'],
            'analytics' => ['core', 'tasks', 'crm', 'invoicing', 'marketing'],
            'communication' => ['core'],
            'files' => ['core'],
            'search' => ['core', 'tasks', 'crm', 'invoicing', 'marketing'],
        ];

        return $dependencies[$service] ?? [];
    }

    /**
     * Check if all dependencies are healthy
     */
    public static function areDependenciesHealthy(string $service): bool
    {
        $dependencies = self::getServiceDependencies($service);

        foreach ($dependencies as $dependency) {
            if (! self::isServiceHealthy($dependency)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get service status summary
     */
    public static function getServiceStatusSummary(): array
    {
        $summary = [];

        foreach (array_keys(self::SERVICE_ENDPOINTS) as $service) {
            $summary[$service] = [
                'healthy' => self::isServiceHealthy($service),
                'endpoint' => self::getServiceEndpoint($service),
                'dependencies' => self::getServiceDependencies($service),
                'dependencies_healthy' => self::areDependenciesHealthy($service),
            ];
        }

        return $summary;
    }
}
