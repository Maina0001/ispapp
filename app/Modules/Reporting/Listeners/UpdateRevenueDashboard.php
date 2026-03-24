<?php

namespace Modules\Reporting\Listeners;

use Modules\Payments\Events\PaymentReceived;
use Modules\Reporting\Services\ReportingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateRevenueDashboard implements ShouldQueue
{
    /**
     * The name of the queue the listener should be run on.
     */
    public string $queue = 'reporting';

    public function __construct(
        protected ReportingService $reportingService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        // Future Multi-tenant: app(TenantContext::class)->setTenantId($event->tenant_id);

        // Orchestrate: Update the daily/monthly revenue aggregates
        $this->reportingService->incrementDailyRevenueMetrics(
            $event->payment->amount,
            $event->payment->created_at
        );
    }
}