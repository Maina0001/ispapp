<?php

namespace Modules\Billing\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Billing\Models\Invoice;

/**
 * Indicates that a new invoice has been generated and is ready for notification.
 */
class InvoiceGenerated
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    /**
     * @param Invoice $invoice The newly generated invoice model.
     */
    public function __construct(public Invoice $invoice)
    {
        $this->tenant_id = $invoice->tenant_id;
    }
}