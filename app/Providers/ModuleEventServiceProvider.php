<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Payments\Events\PaymentReceived;
use Modules\Network\Listeners\ProvisionInternetAccess;
use Modules\Billing\Listeners\SettleInvoiceAfterPayment;

class ModuleEventServiceProvider extends ServiceProvider
{
    /**
     * The event-to-listener mappings for the entire ISP system.
     * * We map a single "Source of Truth" (PaymentReceived) to multiple
     * Domain Listeners (Network, Billing, etc.)
     */
    protected $listen = [
        PaymentReceived::class => [
            // 1. Network Module: Handles hardware/Radius/MikroTik provisioning
            ProvisionInternetAccess::class,

            // 2. Billing Module: Handles Invoices, Ledger, and Financial reconciliation
            SettleInvoiceAfterPayment::class,

            /* Future: 
             * \Modules\Notifications\Listeners\SendConfirmationSms::class,
             */
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}