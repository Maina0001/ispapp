<?php

namespace App\Core\Jobs\Middleware;

use App\Core\TenantContext;
use Illuminate\Support\Facades\Log;

class RestoreTenantContext
{
    /**
     * @param int|string|null $tenantId The ID to restore
     */
    public function __construct(protected $tenantId) {}

    /**
     * Process the queued job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        if ($this->tenantId) {
            // 1. Inject the ID into the Singleton
            // This activates Global Scopes in your BaseModels
            app(TenantContext::class)->setTenantId($this->tenantId);

            Log::debug("Tenant Context Restored for Job", [
                'job' => get_class($job),
                'tenant_id' => $this->tenantId
            ]);
        }

        try {
            return $next($job);
        } finally {
            // Optional: Clear context after job finishes to prevent 
            // context bleeding in long-running worker processes.
            app(TenantContext::class)->setTenantId(null);
        }
    }
}