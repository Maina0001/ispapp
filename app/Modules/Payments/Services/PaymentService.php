<?php

namespace Modules\Payments\Services;

use App\Core\Abstract\BaseService;
use Modules\Billing\Events\InvoicePaid;
use Modules\Billing\Models\Invoice;
use Modules\Payments\Events\PaymentReceived;
use Modules\Payments\Models\Payment;

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
                ->orderBy('due_date', 'asc')
                ->get();

            $remainingFunds = (float) $payment->amount;

            foreach ($invoices as $invoice) {
                if ($remainingFunds <= 0) {
                    break;
                }

                $currentBalance = (float) $invoice->balance;
                $paymentAmount  = min($remainingFunds, $currentBalance);

                if ($paymentAmount <= 0) {
                    continue;
                }

                // Record the link in the pivot table
                $invoice->payments()->attach($payment->id, [
                    'amount_applied' => $paymentAmount,
                    'tenant_id'      => $payment->tenant_id,
                ]);

                // Decrement balance and update status if fully settled
                $newBalance = $currentBalance - $paymentAmount;

                $invoice->update([
                    'balance' => max(0, $newBalance),
                    'status'  => $newBalance <= 0 ? 'paid' : $invoice->status,
                    'paid_at' => $newBalance <= 0 ? now() : $invoice->paid_at,
                ]);

                $remainingFunds -= $paymentAmount;

                if ($newBalance <= 0) {
                    // Fire with a fresh instance so listeners see committed data.
                    event(new InvoicePaid($invoice->fresh()));
                }
            }

            // Single payment-level event. ProvisionInternetAccess listens to this.
            event(new PaymentReceived($payment));
        });
    }
}