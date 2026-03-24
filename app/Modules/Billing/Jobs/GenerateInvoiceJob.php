<?php

namespace Modules\Billing\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;
use Modules\Billing\Services\BillingService;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Subscription $subscription)
    {
        $this->tenant_id = $subscription->tenant_id;
    }

    public function handle(BillingService $billingService): void
    {
        // Future Multi-tenant: TenantContext::set($this->tenant_id);
        $billingService->generateInvoiceForCustomer($this->subscription->customer);
    }
}