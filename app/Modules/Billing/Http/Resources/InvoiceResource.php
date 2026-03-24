<?php

namespace Modules\Billing\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\Http\Resources\CustomerResource;

/**
 * Class InvoiceResource
 * Transforms the Invoice model into a professional financial statement.
 *
 * @package Modules\Billing\Http\Resources
 * @mixin \Modules\Billing\Models\Invoice
 */
class InvoiceResource extends JsonResource
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
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'status'         => $this->status, // e.g., unpaid, paid, overdue, void
            
            // Financial Totals
            'amount_subtotal' => (float) $this->subtotal,
            'amount_tax'      => (float) $this->tax_amount,
            'amount_total'    => (float) $this->total_amount,
            'currency'        => $this->currency ?? 'KES',
            
            // Dates
            'billing_period' => [
                'start' => $this->period_start?->toDateString(),
                'end'   => $this->period_end?->toDateString(),
            ],
            'due_at'         => $this->due_date?->toIso8601String(),
            'paid_at'        => $this->paid_at?->toIso8601String(),

            // Conditional Relationships
            'customer'       => new CustomerResource($this->whenLoaded('customer')),
            'items'          => InvoiceItemResource::collection($this->whenLoaded('items')),

            // Standard Metadata
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}