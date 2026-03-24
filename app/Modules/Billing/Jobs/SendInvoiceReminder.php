<?php

namespace Modules\Billing\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Invoice;
use Modules\Customer\Services\NotificationService;

class SendInvoiceReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Invoice $invoice)
    {
        $this->tenant_id = $invoice->tenant_id;
    }

    public function handle(NotificationService $notificationService): void
    {
        // Calls the notification service to build the message and send via gateway
        $notificationService->sendInvoiceReminder($this->invoice->customer, $this->invoice);
    }
}