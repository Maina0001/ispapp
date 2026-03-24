<?php

namespace Modules\Reporting\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RevenueReportResource
 * Transforms aggregated financial data for period-based income reporting.
 * * @package Modules\Reporting\Http\Resources
 */
class RevenueReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'period' => [
                'label' => $this->period_label, // e.g., "Jan 2026"
                'start' => $this->start_date,
                'end'   => $this->end_date,
            ],
            'financials' => [
                'total_invoiced'  => (float) $this->total_invoiced,
                'total_collected' => (float) $this->total_collected,
                'tax_collected'   => (float) $this->tax_amount,
                'collection_rate' => round(($this->total_collected / max($this->total_invoiced, 1)) * 100, 2) . '%',
            ],
            'transaction_count' => (int) $this->payment_count,
            'top_payment_method' => $this->dominant_method,
        ];
    }
}