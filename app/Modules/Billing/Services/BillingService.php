<?php

namespace App\Modules\Billing\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Customer\Models\Customer;
use App\Modules\Billing\Models\Invoice;

class BillingService extends BaseService
{
    public function __construct(
        protected InvoiceGenerator $generator,
        protected SuspensionService $suspension
    ) {}

    public function generateInvoiceForCustomer(Customer $customer): void
    {
        $this->transactional(function () use ($customer) {
            $customer->subscriptions()->where('status', 'active')->each(function ($sub) {
                $this->generator->buildInvoice($sub);
            });
        });
    }

    public function generateMonthlyInvoices(): void
    {
        // Future Multi-tenant Note: Query should be scoped by tenant via the Job context
        Subscription::where('status', 'active')
            ->chunk(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    $this->generator->buildInvoice($subscription);
                }
            });
    }

    public function suspendOverdueSubscriptions(): void
    {
        Subscription::where('status', 'active')
            ->whereHas('customer.invoices', function ($query) {
                $query->where('status', 'unpaid')->where('due_at', '<', now());
            })->each(function ($subscription) {
                $this->suspension->suspendSubscription($subscription);
            });
    }
}