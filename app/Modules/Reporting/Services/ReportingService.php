<?php

namespace App\Modules\Reporting\Services;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Payments\Models\Payment;
use App\Modules\Network\Models\RadiusAccounting;
use App\Modules\Customer\Models\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingService
{
    /**
     * Generate revenue summary by tenant.
     */
    public function generateRevenueReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'total_invoiced' => Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'total_collected' => Payment::whereBetween('paid_at', [$startDate, $endDate])->sum('amount'),
            'tax_summary' => Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->selectRaw('SUM(total_amount * 0.16) as vat') // Assuming 16% VAT
                ->first(),
        ];
    }

    /**
     * Track network-wide data consumption.
     */
    public function generateUsageReport(): array
    {
        return RadiusAccounting::select('username')
            ->selectRaw('SUM(acctinputoctets)/1073741824 as upload_gb')
            ->selectRaw('SUM(acctoutputoctets)/1073741824 as download_gb')
            ->groupBy('username')
            ->get()
            ->toArray();
    }

    public function generateCustomerReport(): array
    {
        return [
            'active_subscribers' => Customer::where('status', 'active')->count(),
            'churn_rate' => Customer::where('status', 'inactive')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ];
    }
}