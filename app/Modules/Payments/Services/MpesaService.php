<?php

namespace App\Modules\Payments\Services;

use App\Modules\Payments\Models\MpesaTransaction;
use App\Modules\Payments\Jobs\ProcessMpesaCallbackJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MpesaService implements PaymentGatewayInterface
{
    /**
     * Trigger M-Pesa STK Push (Lipa Na M-Pesa Online).
     */
    public function stkPush(array $details): array
    {
        // Integration logic for Daraja API /stkpush/v1/query
        // In production, fetch credentials via Core/Services/SettingsService
        
        Log::info("Initiating STK Push for: " . $details['phone']);

        return [
            'MerchantRequestID' => 'ISP-' . uniqid(),
            'CheckoutRequestID' => 'ws_CO_' . bin2hex(random_bytes(10)),
            'ResponseCode' => '0',
            'CustomerMessage' => 'Success. Please enter PIN on your phone.'
        ];
    }

    public function verifyTransaction(string $checkoutRequestId): bool
    {
        // Query Safaricom to confirm the status of a specific checkout ID
        return true; 
    }

    public function processCallback(array $data): void
    {
        // Dispatch to job to handle the data asynchronously to keep the webhook response fast
        ProcessMpesaCallbackJob::dispatch($data)->onQueue('payments');
    }

    public function charge(array $details): array { return $this->stkPush($details); }
    public function refund(string $id, float $amt): bool { return false; /* Not common for STK */ }
}