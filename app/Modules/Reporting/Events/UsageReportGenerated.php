<?php

namespace Modules\Reporting\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Triggered when bandwidth consumption data has been aggregated per customer.
 */
class UsageReportGenerated
{
    use SerializesModels;

    public ?int $tenant_id;

    /**
     * @param array $usageStats Keyed array of usernames and their GB consumption.
     * @param string $period (e.g., 'daily', 'monthly')
     * @param int|null $tenant_id The tenant context for the report.
     */
    public function __construct(
        public array $usageStats,
        public string $period,
        ?int $tenant_id = null
    ) {
        $this->tenant_id = $tenant_id;
    }
}