<?php

namespace Modules\Network\Listeners;

use Modules\Payments\Events\PaymentCompleted;
use Modules\Network\Services\ProvisioningService;
use Illuminate\Support\Facades\Log;

class ActivateInternetAccess
{
    /**
     * Laravel's Service Container automatically injects the ProvisioningService.
     */
    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    public function handle(PaymentCompleted $event): void
    {
        $transaction = $event->transaction;

        Log::info("Event Bridge: Received PaymentCompleted for Transaction #{$transaction->id}");

        try {
            // Business Logic: Activate the user on the network
            // We assume the transaction object has the phone/mac needed
            $this->provisioningService->activateCustomerByPhone($transaction->phone_number);
            
        } catch (\Exception $e) {
            Log::error("Event Bridge Error: Failed to activate network for Transaction #{$transaction->id}. Error: " . $e->getMessage());
        }
    }
}