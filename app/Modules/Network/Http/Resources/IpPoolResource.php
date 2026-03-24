<?php

namespace Modules\Network\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IpPoolResource
 * Transforms IP range and utilization data.
 */
class IpPoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'pool_name'         => $this->name,
            'subnet'            => $this->subnet,
            'gateway'           => $this->gateway,
            'pool_type'         => $this->pool_type, // static, dynamic, cgnat
            
            // Computed utilization metrics
            'total_ips'         => (int) $this->total_count,
            'used_ips'          => (int) $this->used_count,
            'usage_percentage'  => round(($this->used_count / max($this->total_count, 1)) * 100, 2) . '%',

            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}