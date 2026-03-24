<?php

namespace App\Modules\Billing\Services;

use App\Modules\Billing\Models\Subscription;
use Carbon\Carbon;

class ProrationCalculator
{
    /**
     * Calculates the cost based on remaining days in the month.
     * Logic: (Plan Price / Days in Month) * Remaining Days
     */
    public function calculateProration(Subscription $subscription): float
    {
        $planPrice = $subscription->plan->price;
        $now = Carbon::now();

        // If subscription started mid-month
        if ($subscription->created_at->isCurrentMonth()) {
            $daysInMonth = $now->daysInMonth;
            $remainingDays = $now->diffInDays($now->copy()->endOfMonth()) + 1;
            
            $dailyRate = $planPrice / $daysInMonth;
            return round($dailyRate * $remainingDays, 2);
        }

        return (float) $planPrice;
    }
}