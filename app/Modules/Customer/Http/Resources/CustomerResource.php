<?php

namespace Modules\Customer\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Billing\Http\Resources\SubscriptionResource;
use Modules\Network\Http\Resources\RadiusAccountResource;

/**
 * Class CustomerResource
 * * Standardizes the Customer model output for API responses.
 * Handles data transformation and conditional relationship loading 
 * while maintaining tenant-aware data integrity.
 * * @package Modules\Customer\Http\Resources
 * @mixin \Modules\Customer\Models\Customer
 */
class CustomerResource extends JsonResource
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
            // Primary Identifiers
            'id' => $this->id,
            
            // Profile Information
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}"),
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'physical_address' => $this->physical_address,
            
            // Status & Financials
            'status' => $this->status, // e.g., active, suspended, inactive
            'balance' => (float) ($this->balance ?? 0.00),
            'is_overdue' => (bool) ($this->is_overdue ?? false),

            /**
             * Nested Relationships
             * Using whenLoaded() prevents N+1 query issues by only 
             * including data if it was explicitly eager-loaded in the controller.
             */
            'active_subscription' => new SubscriptionResource($this->whenLoaded('activeSubscription')),
            'subscriptions' => SubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'network_account' => new RadiusAccountResource($this->whenLoaded('radiusAccount')),

            // Standard ISO Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            
            /**
             * Note: tenant_id is excluded from the transformation to maintain 
             * security and focus the API on domain-specific data.
             */
        ];
    }
}