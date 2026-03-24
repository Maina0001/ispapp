<?php

namespace Modules\Reporting\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\Http\Resources\CustomerSummaryResource;

/**
 * Class OutstandingInvoiceResource
 * Focuses on aging debt and overdue receivables.
 */
class OutstandingInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'invoice_id'      => $this->id,
            'invoice_no'      => $this->invoice_number,
            'amount_due'      => (float) $this->total_amount,
            'due_date'        => $this->due_date?->toDateString(),
            'days_overdue'    => (int) $this->days_overdue,
            'severity'        => $this->getSeverityLevel(), // e.g., "high", "critical"
            
            // Nested lightweight customer info
            'customer'        => new CustomerSummaryResource($this->whenLoaded('customer')),
            
            'status'          => $this->status,
            'last_reminder_sent' => $this->last_reminder_at?->toIso8601String(),
        ];
    }

    /**
     * Business logic for categorization in reports.
     */
    protected function getSeverityLevel(): string
    {
        return match (true) {
            $this->days_overdue > 30 => 'critical',
            $this->days_overdue > 7  => 'high',
            default                  => 'normal',
        };
    }
}