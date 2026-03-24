<?php

namespace Modules\Reporting\Listeners;

use Modules\Reporting\Events\UsageReportGenerated;
use Modules\Reporting\Services\ReportingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArchiveHistoricalUsage implements ShouldQueue
{
    public string $queue = 'reporting';

    public function __construct(
        protected ReportingService $reportingService
    ) {}

    public function handle(UsageReportGenerated $event): void
    {
        // Orchestrate: Move raw usage data to historical archive table
        // to keep the active production tables high-performance.
        $this->reportingService->archiveProcessedUsageData(
            $event->usageStats,
            $event->period
        );
    }
}