<?php

namespace Modules\Network\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NasDeviceResource
 * Transforms hardware details for the Network Access Servers.
 */
class NasDeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'identifier'    => $this->shortname,
            'ip_address'    => $this->nasname,
            'vendor_type'   => $this->type, // e.g., mikrotik
            'description'   => $this->description,
            
            // Real-time metrics
            'active_sessions' => (int) ($this->active_sessions_count ?? 0),
            
            // Standard Metadata
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}