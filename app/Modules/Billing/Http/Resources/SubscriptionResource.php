<?php

namespace Modules\Billing\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Network\Http\Resources\BandwidthProfileResource;

/**
 * Class SubscriptionResource
 * Transforms recurring subscription data.
 */
class SubscriptionResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status, // e.g., active, suspended, cancelled
            'billing_cycle' => $this->billing_cycle, // e.g., monthly
            'price'         => (float) $this->amount,
            
            // Lifecycle Dates
            'start_date'    => $this->start_date?->toDateString(),
            'next_bill_at'  => $this->next_billing_date?->toDateString(),
            'canceled_at'   => $this->canceled_at?->toIso8601String(),

            // Conditional Network Profile
            'plan'          => new BandwidthProfileResource($this->whenLoaded('plan')),

            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}