<?php

namespace Modules\Network\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Payments\Events\PaymentReceived;
use Modules\Network\Services\ProvisioningService;
use Illuminate\Support\Facades\Log;

class ProvisionInternetAccess implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public $queue = 'network';

    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $transaction = $event->transaction;

        // 1. Audit check: Ensure we have the necessary data to provision
        if (!$transaction->mac_address || !$transaction->plan_id) {
            Log::error("Provisioning failed: Missing MAC or Plan ID for Transaction #{$transaction->id}");
            return;
        }

        try {
            // 2. Hand off to the ProvisioningService
            // This service coordinates RadiusManager and BandwidthManager
            $this->provisioningService->provisionCustomerService(
                $transaction->customer, // Assumes relationship on MpesaTransaction model
                ['plan_id' => $transaction->plan_id]
            );

            Log::info("Provisioning triggered for MAC: {$transaction->mac_address} via Transaction: {$transaction->mpesa_receipt}");

        } catch (\Exception $e) {
            Log::error("Critical Provisioning Error: " . $e->getMessage());
            
            // If it fails, the job will stay in the 'failed_jobs' table for manual retry
            throw $e; 
        }
    }
}