<?php

namespace Modules\Billing\Listeners;

use Modules\Payments\Events\PaymentReceived;
use Modules\Billing\Models\Invoice;
use Illuminate\Support\Facades\Log;

class SettleInvoiceAfterPayment
{
    public function handle(PaymentReceived $event): void
    {
        $transaction = $event->transaction;

        // Find the most recent unpaid invoice for this customer
        $invoice = Invoice::where('customer_id', $transaction->customer_id)
            ->where('status', 'unpaid')
            ->latest()
            ->first();

        if ($invoice) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'mpesa_receipt' => $transaction->mpesa_receipt
            ]);

            Log::info("Billing Module: Invoice #{$invoice->id} settled via M-Pesa.");
        }
    }
}