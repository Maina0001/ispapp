<?php

namespace Modules\Reporting\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Represents the completion of a subscriber growth and churn analysis.
 */
class CustomerReportGenerated
{
    use SerializesModels;

    public ?int $tenant_id;

    /**
     * @param array $metrics Contains active count, churn rate, and new leads.
     * @param int|null $tenant_id The tenant context for the report.
     */
    public function __construct(
        public array $metrics,
        ?int $tenant_id = null
    ) {
        $this->tenant_id = $tenant_id;
    }
}