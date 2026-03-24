<?php

namespace Modules\Payments\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Billing\Http\Resources\InvoiceResource;
use Modules\Customer\Http\Resources\CustomerResource;

/**
 * Class PaymentResource
 * * Transforms the Payment model into a standardized JSON structure.
 * Handles cross-module relationships for Customers and Invoices.
 * * @package Modules\Payments\Http\Resources
 * @mixin \Modules\Payments\Models\Payment
 */
class PaymentResource extends JsonResource
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
            'transaction_reference' => $this->reference,
            'amount' => (float) $this->amount,
            'currency' => $this->currency ?? 'KES',
            'payment_method' => $this->payment_method,
            'status' => $this->status, // e.g., completed, pending, failed
            
            /**
             * Conditional Relationships
             * Included only if eager-loaded in the Controller.
             */
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'invoice'  => new InvoiceResource($this->whenLoaded('invoice')),
            'gateway_details' => new MpesaTransactionResource($this->whenLoaded('mpesaTransaction')),

            // Standard Metadata
            'paid_at'    => $this->paid_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}