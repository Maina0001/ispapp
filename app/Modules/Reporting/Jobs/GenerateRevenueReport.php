<?php

namespace Modules\Reporting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Reporting\Services\ReportingService;
use Carbon\Carbon;

class GenerateRevenueReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int|null Context for multi-tenancy tracking
     */
    public ?int $tenant_id;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Carbon $startDate,
        public Carbon $endDate,
        ?int $tenant_id = null
    ) {
        $this->tenant_id = $tenant_id;
    }

    /**
     * Execute the job.
     */
    public function handle(ReportingService $reportingService): void
    {
        // Future Multi-tenant: app(TenantContext::class)->setTenantId($this->tenant_id);
        
        $reportingService->generateRevenueReport($this->startDate, $this->endDate);
    }
}