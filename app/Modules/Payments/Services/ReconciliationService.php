<?php

namespace App\Modules\Payments\Services;

class ReconciliationService
{
    /**
     * This service listens for PaymentCompleted and clears outstanding Invoices.
     */
    public function settleInvoice($transaction)
    {
        // Logic to find the oldest unpaid invoice for this customer and mark as PAID
    }
}