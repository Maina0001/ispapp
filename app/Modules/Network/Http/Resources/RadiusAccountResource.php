<?php

namespace Modules\Network\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\Http\Resources\CustomerResource;

/**
 * Class RadiusAccountResource
 * * Transforms the RADIUS account details for API consumption.
 * Abstracts technical authentication attributes into a clean profile.
 * * @package Modules\Network\Http\Resources
 * @mixin \Modules\Network\Models\RadiusAccount
 */
class RadiusAccountResource extends JsonResource
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
            'username'       => $this->username,
            'status'         => $this->status, // active, suspended, expired
            'is_online'      => (bool) $this->is_online,
            'current_ip'     => $this->current_ip_address,
            'mac_address'    => $this->calling_station_id,
            
            // Speed Profile (Nested)
            'bandwidth'      => new BandwidthProfileResource($this->whenLoaded('bandwidthProfile')),
            
            // Linked Customer
            'customer'       => new CustomerResource($this->whenLoaded('customer')),

            // Standard Metadata
            'last_login_at'  => $this->last_login?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}