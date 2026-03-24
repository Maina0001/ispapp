<?php

namespace App\Modules\Reporting\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Payments\Models\MpesaTransaction;
use App\Modules\Payments\Models\Payment;
use App\Modules\Reporting\Jobs\AuditMpesaDiscrepancyJob;
use Illuminate\Support\Facades\Log;

class ReconciliationService extends BaseService
{
    /**
     * Compare M-Pesa Transactions with System Payments.
     */
    public function reconcileMpesaTransactions(): void
    {
        // Get successful M-Pesa hits that don't have a corresponding Payment record
        $unreconciled = MpesaTransaction::where('status', 'success')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('payments')
                      ->whereColumn('payments.transaction_reference', 'mpesa_transactions.mpesa_receipt_number');
            })
            ->get();

        foreach ($unreconciled as $transaction) {
            Log::warning("Reconciliation mismatch found for Receipt: {$transaction->mpesa_receipt_number}");
            
            // Dispatch job to manually create payment or notify admin
            AuditMpesaDiscrepancyJob::dispatch($transaction);
        }
    }
}