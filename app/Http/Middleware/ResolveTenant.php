<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     * * This middleware identifies which ISP/Tenant is active based on the URL
     * or defaults to the primary one if accessed via IP (192.168.1.159).
     */
    public function handle(Request $request, Closure $next): Response
    {
      // 1. Allow API routes to skip the captive portal redirect
    if ($request->is('api/*')) {
        return $next($request);
    }
        $host = $request->getHost();

        // 1. Try to match by slug (e.g., kariuki-isp) 
        // 2. OR fallback to the first active tenant in the database
        $tenant = Tenant::where('slug', $host)
            ->orWhere('is_active', true)
            ->first();

        // If no tenant exists at all, the system cannot function
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant Resolution Failed',
                'message' => 'No active ISP configuration found in the database.'
            ], 404);
        }

        /**
         * GLOBAL DATA SHARING
         * sharing 'tenant' allows you to use {{ $tenant->name }} in ANY blade file
         * without passing it manually from the controller every time.
         */
        view()->share('tenant', $tenant);

        // Attach to the request object for use in Controllers if needed
        $request->merge(['tenant' => $tenant]);

        return $next($request);
    }
}
