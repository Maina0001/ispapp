<?php

namespace Modules\Network\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BandwidthProfileResource
 * Transforms technical speed limits and FUP rules.
 */
class BandwidthProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'download_speed'   => "{$this->download_limit} Kbps",
            'upload_speed'     => "{$this->upload_limit} Kbps",
            'burst_enabled'    => (bool) $this->burst_limit,
            'is_shared'        => (bool) $this->is_shared,
            
            // Only expose internal hardware identifiers to authorized admins
            'mikrotik_name'    => $this->when($request->user()?->can('manage-network'), $this->mikrotik_name),

            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}