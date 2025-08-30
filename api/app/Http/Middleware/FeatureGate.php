<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class FeatureGate
{
    public function handle(Request $request, Closure $next, string $feature): SymfonyResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $tenant = $user->tenant;
        
        if (!$tenant) {
            return response()->json(['message' => 'No tenant associated'], Response::HTTP_FORBIDDEN);
        }

        if (!$tenant->canAccessFeature($feature)) {
            return response()->json([
                'message' => "Feature '{$feature}' not available on your plan",
                'feature' => $feature,
                'plan' => $tenant->plan,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
