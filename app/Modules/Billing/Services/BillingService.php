<?php

namespace Modules\Billing\Services;

use App\Core\Abstract\BaseService;
use Modules\Billing\Models\Invoice;
use Modules\Customer\Models\Customer;
use Carbon\Carbon;

class BillingService extends BaseService
{
    /**
     * Generate an invoice for a specific customer and plan.
     */
    public function generateInvoice(Customer $customer, $plan): Invoice
    {
        return $this->transactional(function () use ($customer, $plan) {
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'amount'      => $plan->price,
                'tax_amount'  => $plan->price * 0.16, // 16% VAT Example
                'total_due'   => $plan->price * 1.16,
                'due_date'    => Carbon::now()->addDays(3),
                'status'      => 'unpaid',
                'tenant_id'   => $customer->tenant_id,
            ]);

            $this->logActivity("Invoice #{$invoice->id} generated for Customer: {$customer->id}");

            return $invoice;
        });
    }
}