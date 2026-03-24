<?php

namespace Modules\Payments\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MpesaTransactionResource
 * * Transforms the technical M-Pesa transaction details for API consumption.
 * Abstracts the raw Daraja API response into a readable format.
 * * @package Modules\Payments\Http\Resources
 * @mixin \Modules\Payments\Models\MpesaTransaction
 */
class MpesaTransactionResource extends JsonResource
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
            'id' => $this->id,
            'merchant_request_id' => $this->merchant_request_id,
            'checkout_request_id' => $this->checkout_request_id,
            'receipt_number' => $this->mpesa_receipt_number,
            'phone_number' => $this->phone_number,
            
            // Result interpretation
            'result_code' => (int) $this->result_code,
            'result_description' => $this->result_desc,
            'is_successful' => $this->result_code === 0,
            
            // The raw payload is generally hidden unless requested by an admin
            'raw_callback_data' => $this->when($request->user()?->can('view-raw-logs'), $this->raw_payload),

            // Standard Metadata
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}