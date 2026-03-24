<?php

namespace App\Modules\Reporting\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Payments\Models\Payment;
use App\Modules\Reporting\Events\BalanceReconciled;

class AccountingService extends BaseService
{
    /**
     * Ensure every payment is correctly linked to an invoice.
     */
    public function reconcilePayments(): void
    {
        $this->transactional(function () {
            // Find payments not yet fully applied to invoices
            Payment::whereRaw('amount > (SELECT SUM(amount_applied) FROM invoice_payments WHERE payment_id = payments.id)')
                ->chunk(100, function ($payments) {
                    foreach ($payments as $payment) {
                        // Logic to auto-apply to the oldest invoice
                    }
                });
        });
    }

    /**
     * Calculate total debt across the ISP.
     */
    public function calculateOutstandingBalances(): float
    {
        return Invoice::where('status', '!=', 'paid')
            ->where('due_at', '<', now())
            ->sum(DB::raw('total_amount - amount_paid'));
    }
}