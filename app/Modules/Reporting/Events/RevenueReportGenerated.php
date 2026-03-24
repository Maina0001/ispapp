<?php

namespace Modules\Reporting\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Carbon\Carbon;

/**
 * Indicates that the financial aggregation for a specific period is complete.
 */
class RevenueReportGenerated
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    /**
     * @param array $reportData Summary of revenue, taxes, and collections.
     * @param Carbon $startDate Start of the reporting period.
     * @param Carbon $endDate End of the reporting period.
     * @param int|null $tenant_id The tenant context for the report.
     */
    public function __construct(
        public array $reportData,
        public Carbon $startDate,
        public Carbon $endDate,
        ?int $tenant_id = null
    ) {
        $this->tenant_id = $tenant_id;
    }
}