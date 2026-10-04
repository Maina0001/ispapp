<?php

namespace Modules\Billing\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Invoice;

/**
 * Triggered when an invoice status changes to 'paid'.
 *
 * Dispatched inside PaymentService::applyPaymentToInvoice() within a DB
 * transaction. ShouldDispatchAfterCommit ensures listeners only run once
 * the transaction has committed and the invoice is truly persisted as paid.
 */
class InvoicePaid implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Invoice $invoice)
    {
        $this->tenant_id = $invoice->tenant_id;
    }
}