<?php

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\Customer;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Future Multi-tenant Note: 
     * Tenant-specific SMS templates should be fetched here using SettingsService.
     */
    public function sendWelcomeNotification(Customer $customer): void
    {
        $message = "Hello {$customer->first_name}, welcome to our High-Speed Fiber. Your account is now active!";
        
        // Dispatch to Queue to prevent blocking the UI
        // SendSmsJob::dispatch($customer->phone_number, $message, $customer->tenant_id);
        
        Log::info("Welcome SMS logged for Customer: {$customer->id}");
    }

    public function sendSuspensionNotification(Customer $customer): void
    {
        $message = "Dear {$customer->first_name}, your internet has been suspended due to an unpaid invoice. Please pay via M-Pesa to restore service.";
        
        // Dispatch to Queue
        // SendSmsJob::dispatch($customer->phone_number, $message, $customer->tenant_id);
        
        Log::warning("Suspension SMS logged for Customer: {$customer->id}");
    }
}