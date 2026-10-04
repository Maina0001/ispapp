<?php

namespace Modules\Network\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Billing\Events\SubscriptionCreated;
use Modules\Billing\Models\Subscription;
use Modules\Network\Models\ServicePlan;
use Modules\Payments\Events\PaymentReceived;

class ProvisionInternetAccess implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'network';

    public int $tries = 3;

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(PaymentReceived $event): void
    {
        $payment  = $event->payment;
        $customer = $payment->customer;

        if (! $customer) {
            Log::error("Provisioning: Payment {$payment->id} has no customer.");
            return;
        }

        // Resolve plan — prefer the payment's plan_id, fall back to customer's default
        $planId = $payment->plan_id ?? $customer->plan_id;
        $plan   = ServicePlan::find($planId);

        if (! $plan) {
            Log::error("Provisioning: Plan ID {$planId} not found for Payment {$payment->id}.");
            return;
        }

        // Renewal-safe: if the customer has an unexpired active subscription,
        // extend its expiry rather than overwriting it.
        $existing = Subscription::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();

        $startsAt  = $existing?->starts_at ?? now();
        $expiresAt = $existing
            ? $existing->expires_at->copy()->addMinutes($plan->duration_minutes)
            : Carbon::now()->addMinutes($plan->duration_minutes);

        $subscription = Subscription::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'status'      => 'active',
            ],
            [
                'tenant_id'  => $payment->tenant_id,
                'plan_id'    => $plan->id,
                'starts_at'  => $startsAt,
                'expires_at' => $expiresAt,
            ]
        );

        Log::info("Subscription {$subscription->id} ready for customer {$customer->id}, expires {$expiresAt->toDateTimeString()}");

        // Hand off to the network layer.
        // ProvisionNetworkAccess listens to this and is the ONLY place
        // that calls ProvisioningService::provisionCustomerService().
        event(new SubscriptionCreated($subscription));
    }
}