<?php

namespace Modules\Network\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Billing\Events\InvoicePaid;
use Modules\Network\Services\RadiusManager;

class RestoreNetworkAccess implements ShouldQueue
{
    public string $queue = 'network';

    public function __construct(
        protected RadiusManager $radiusManager,
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $customer = $event->invoice->customer;

        if (! $customer) {
            Log::warning('RestoreNetworkAccess: invoice has no customer.', [
                'invoice_id' => $event->invoice->id,
            ]);
            return;
        }

        // If the customer still has overdue invoices, leave them suspended.
        if ($customer->hasOverdueInvoices()) {
            return;
        }

        $account = $customer->radiusAccount;

        if (! $account) {
            // No RADIUS account yet — ProvisionInternetAccess will create
            // one when the payment is processed.
            Log::info('RestoreNetworkAccess: no RADIUS account to resume.', [
                'customer_id' => $customer->id,
            ]);
            return;
        }

        $this->radiusManager->resumeRadiusAccount($account);
    }
}