<?php

namespace Modules\Reporting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Reporting\Services\ReportingService;

class GenerateUsageReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(?int $tenant_id = null)
    {
        $this->tenant_id = $tenant_id;
    }

    public function handle(ReportingService $reportingService): void
    {
        // Aggregates raw octets into GB for the dashboard
        $reportingService->generateUsageReport();
    }
}