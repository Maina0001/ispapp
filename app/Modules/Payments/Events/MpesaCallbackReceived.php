<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Represents the raw data payload received from the M-Pesa Callback URL.
 */
class MpesaCallbackReceived
{
    use SerializesModels;

    /**
     * @param array $payload The raw JSON data from Safaricom.
     * @param int|null $tenant_id The identified tenant for this callback.
     */
    public function __construct(
        public array $payload,
        public ?int $tenant_id = null
    ) {}
}