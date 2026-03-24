<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Core\Context\TenantContext;
use App\Modules\Customer\Models\Tenant; // Assume a 'tenants' table exists
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Identification: Logic to find the tenant
        // Example: isp1.billingapp.com or a custom header 'X-Tenant-ID'
        $identifier = $request->header('X-Tenant-ID') ?? $request->getHost();

        // 2. Resolution: Fetch from Database
        // For a single ISP system (initially), you might hardcode ID 1 
        // or fetch by domain.
        $tenant = Tenant::where('domain', $identifier)->first();

        if (!$tenant && config('app.env') !== 'local') {
            abort(403, 'Unauthorized Tenant Context.');
        }

        // 3. Injection: Set the context for the rest of the request
        if ($tenant) {
            $this->context->setTenant($tenant);
        }

        return $next($request);
    }
}