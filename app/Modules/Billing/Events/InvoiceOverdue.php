<?php

namespace Modules\Billing\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Invoice;

/**
 * Represents the fact that an invoice has remained unpaid past the due date.
 */
class InvoiceOverdue
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Invoice $invoice)
    {
        $this->tenant_id = $invoice->tenant_id;
    }
}