<?php

namespace App\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Core\TenantContext;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The ID of the ISP (Tenant) this job belongs to.
     */
    public $tenant_id;

    public function __construct($tenant_id = null)
    {
        // Capture the active tenant from the context if not manually passed
        $this->tenant_id = $tenant_id ?? app(TenantContext::class)->getTenantId();
    }

    /**
     * Job Middleware: Restores the Tenant Context before the handle() method runs.
     */
    public function middleware(): array
    {
        return [new \App\Core\Http\Middleware\RestoreTenantContext($this->tenant_id)];
    }
}