<?php

namespace Modules\Reporting\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NetworkUsageResource
 * Transforms technical byte counts into human-readable data metrics.
 */
class NetworkUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Conversion factor from bytes to Gigabytes
        $gbFactor = 1024 ** 3;

        return [
            'username'       => $this->username,
            'consumption'    => [
                'download_gb' => round($this->bytes_out / $gbFactor, 2),
                'upload_gb'   => round($this->bytes_in / $gbFactor, 2),
                'total_gb'    => round(($this->bytes_in + $this->bytes_out) / $gbFactor, 2),
            ],
            'sessions' => [
                'count'          => (int) $this->session_count,
                'total_uptime'   => (int) $this->total_uptime_seconds,
                'average_session'=> round($this->total_uptime_seconds / max($this->session_count, 1), 0) . 's',
            ],
            'period'         => $this->report_period, // e.g., "2026-03"
        ];
    }
}