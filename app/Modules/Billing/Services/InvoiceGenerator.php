<?php

namespace App\Modules\Billing\Services;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Models\Subscription;

class InvoiceGenerator
{
    public function __construct(protected ProrationCalculator $proration) {}

    public function buildInvoice(Subscription $subscription): Invoice
    {
        $items = $this->calculateInvoiceItems($subscription);
        
        $invoice = Invoice::create([
            'customer_id'    => $subscription->customer_id,
            'tenant_id'      => $subscription->tenant_id,
            'total_amount'   => collect($items)->sum('total_price'),
            'status'         => 'unpaid',
            'due_at'         => now()->addDays(5), // Configurable via SettingsService
        ]);

        foreach ($items as $item) {
            $invoice->items()->create($item);
        }

        return $invoice;
    }

    public function calculateInvoiceItems(Subscription $subscription): array
    {
        $amount = $this->proration->calculateProration($subscription);
        
        return [[
            'description' => "Monthly Internet Service: " . $subscription->plan->name,
            'quantity'    => 1,
            'unit_price'  => $amount,
            'total_price' => $amount,
        ]];
    }
}