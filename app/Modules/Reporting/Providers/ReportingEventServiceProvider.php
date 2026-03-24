<?php

namespace Modules\Reporting\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Payments\Events\PaymentReceived;
use Modules\Reporting\Events\UsageReportGenerated;
use Modules\Reporting\Listeners\UpdateRevenueDashboard;
use Modules\Reporting\Listeners\ArchiveHistoricalUsage;

class ReportingEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the Reporting Module.
     */
    protected $listen = [
        PaymentReceived::class => [
            UpdateRevenueDashboard::class,
        ],
        UsageReportGenerated::class => [
            ArchiveHistoricalUsage::class,
        ],
    ];
}