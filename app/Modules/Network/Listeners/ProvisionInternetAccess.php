<?php

namespace Modules\Network\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Payments\Events\PaymentReceived;
use Modules\Network\Services\ProvisioningService;
use Modules\Network\Models\ServicePlan;
use Modules\Billing\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProvisionInternetAccess implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'network';

    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    public function handle(PaymentReceived $event): void
    {
        $transaction = $event->transaction;

        // 1. Resolve the Service Plan (Package)
        $plan = ServicePlan::find($transaction->plan_id);

        if (!$plan) {
            Log::error("Provisioning Error: Plan ID {$transaction->plan_id} not found.");
            return;
        }

        // 2. Calculate Expiry Date
        // We use Carbon to add the duration from the package to the current time
        $expiryDate = Carbon::now()->addMinutes($plan->duration_minutes);

        // 3. Update or Create the Subscription
        // This links the Customer to the Plan and sets the 'Grim Reaper' timer
        $subscription = Subscription::updateOrCreate(
            ['customer_id' => $transaction->customer_id],
            [
                'tenant_id' => $transaction->tenant_id,
                'plan_id'    => $plan->id,
                'status'     => 'active',
                'starts_at'  => now(),
                'expires_at' => $expiryDate,
            ]
        );

        try {
            // 4. Trigger Hardware Provisioning
            // We pass the plan name so the MikroTik adapter can apply the correct speed profile
            $this->provisioningService->provisionCustomerService(
                $transaction->customer,
                $plan
            );

            Log::info("Hotspot Activated: MAC {$transaction->mac_address} expires at {$expiryDate->toDateTimeString()}");

        } catch (\Exception $e) {
            Log::error("Hardware Provisioning Failed: " . $e->getMessage());
            throw $e; 
        }
    }
}