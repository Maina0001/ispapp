<?php

namespace App\Modules\Payments\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Billing\Events\InvoicePaid;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Payments\Events\PaymentReceived;
use App\Modules\Payments\Models\Payment;

class PaymentService extends BaseService
{
    public function __construct(protected MpesaService $mpesa) {}

    /**
     * Start a payment flow for a customer.
     */
    public function initiatePayment(int $customerId, float $amount, string $phone): array
    {
        return $this->mpesa->charge([
            'customer_id' => $customerId,
            'amount'      => $amount,
            'phone'       => $phone,
        ]);
    }

    /**
     * Links a successful payment to one or more unpaid invoices (FIFO).
     * Fires InvoicePaid per settled invoice and PaymentReceived once at the end.
     */
    public function applyPaymentToInvoice(Payment $payment): void
    {
        $this->transactional(function () use ($payment) {
            $invoices = Invoice::where('customer_id', $payment->customer_id)
                ->where('status', '!=', 'paid')
                ->orderBy('due_at', 'asc')
                ->get();

            $remainingFunds = $payment->amount;

            foreach ($invoices as $invoice) {
                if ($remainingFunds <= 0) {
                    break;
                }

                $paymentAmount = min($remainingFunds, $invoice->balance);

                if ($paymentAmount <= 0) {
                    continue;
                }

                // Record the link in the pivot table
                $invoice->payments()->attach($payment->id, [
                    'amount_applied' => $paymentAmount,
                    'tenant_id'      => $payment->tenant_id,
                ]);

                $remainingFunds -= $paymentAmount;

                // Check if invoice is now fully paid
                if ($invoice->refresh()->balance <= 0) {
                    $invoice->update([
                        'status'  => 'paid',
                        'paid_at' => now(),
                    ]);

                    // Triggers RestoreNetworkAccess and any other InvoicePaid listeners.
                    event(new InvoicePaid($invoice));
                }
            }

            // Single payment-level event. ProvisionInternetAccess listens to this.
            event(new PaymentReceived($payment));
        });
    }
}