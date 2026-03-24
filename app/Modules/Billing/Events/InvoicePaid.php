<?php

namespace Modules\Billing\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Invoice;

/**
 * Triggered when an invoice status changes to 'paid'.
 */
class InvoicePaid
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Invoice $invoice)
    {
        $this->tenant_id = $invoice->tenant_id;
    }
}