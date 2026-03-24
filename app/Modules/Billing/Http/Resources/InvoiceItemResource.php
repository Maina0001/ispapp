<?php

namespace Modules\Billing\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class InvoiceItemResource
 * Transforms individual line items within an invoice.
 */
class InvoiceItemResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'description' => $this->description,
            'quantity'    => (float) $this->quantity,
            'unit_price'  => (float) $this->unit_price,
            'total_price' => (float) ($this->quantity * $this->unit_price),
            'is_taxable'  => (bool) $this->is_taxable,
            'item_type'   => $this->type, // e.g., service, fee, hardware
        ];
    }
}