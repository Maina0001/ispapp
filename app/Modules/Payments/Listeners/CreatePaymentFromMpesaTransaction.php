<?php

namespace Modules\Payments\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Events\MpesaTransactionCompleted;
use Modules\Payments\Jobs\ApplyPaymentToInvoice;
use Modules\Payments\Models\Payment;
use Throwable;

class CreatePaymentFromMpesaTransaction implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'payments';
    public int $tries   = 3;

    public function backoff(): array
    {
        return [15, 60, 180];
    }

    public function handle(MpesaTransactionCompleted $event): void
    {
        $txn = $event->transaction;

        // --- Guard 1: receipt number must exist ---
        if (! $txn->mpesa_receipt_number) {
            Log::error('CreatePaymentFromMpesaTransaction: no receipt number.', [
                'transaction_id' => $txn->id,
            ]);
            return;
        }

        // --- Guard 2: idempotency — Payment already exists for this receipt ---
        if (Payment::where('gateway_reference', $txn->mpesa_receipt_number)->exists()) {
            Log::info('CreatePaymentFromMpesaTransaction: payment already exists.', [
                'receipt' => $txn->mpesa_receipt_number,
            ]);
            return;
        }

        // --- Guard 3: customer must exist ---
        if (! $txn->customer_id) {
            Log::error('CreatePaymentFromMpesaTransaction: transaction has no customer.', [
                'transaction_id' => $txn->id,
            ]);
            return;
        }

        try {
            $payment = DB::transaction(function () use ($txn) {
                return Payment::create([
                    'tenant_id'         => $txn->tenant_id,
                    'customer_id'       => $txn->customer_id,
                    'amount'            => $txn->amount,
                    'currency'          => 'KES',
                    'gateway'           => 'mpesa',
                    'gateway_reference' => $txn->mpesa_receipt_number,
                    'plan_id'           => $txn->plan_id,   // if the column exists
                    'status'            => 'completed',
                    'paid_at'           => now(),
                ]);
            });

            // Hand off to the async invoice-application job.
            ApplyPaymentToInvoice::dispatch($payment)->onQueue('payments');

            Log::info('CreatePaymentFromMpesaTransaction: payment created.', [
                'payment_id' => $payment->id,
                'receipt'    => $txn->mpesa_receipt_number,
            ]);
        } catch (Throwable $e) {
            Log::error('CreatePaymentFromMpesaTransaction failed.', [
                'transaction_id' => $txn->id,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}