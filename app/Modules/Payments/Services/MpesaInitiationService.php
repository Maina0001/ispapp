<?php

namespace Modules\Payments\Services;

use Modules\Payments\Interfaces\PaymentInitiatorInterface;
use Modules\Customer\Models\Customer;
use Modules\Network\Models\ServicePlan;
use Modules\Payments\Models\MpesaTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaInitiationService implements PaymentInitiatorInterface
{
    public function initiatePush(Customer $customer, ServicePlan $plan): array
    {
        // 1. Get STK Token (Assuming you have a helper for this)
        $token = $this->generateToken(); 
        
        $checkoutId = 'CH_' . uniqid(); // Placeholder until Safaricom returns one

        try {
            // 2. Log intent to pay (Creating a 'pending' transaction)
            $transaction = MpesaTransaction::create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'phone' => $customer->phone_number,
                'checkout_request_id' => $checkoutId, // Temporary
                'status' => 'pending'
            ]);

            // 3. Make the actual API call to Safaricom
            $response = Http::withToken($token)->post(config('services.mpesa.stk_push_url'), [
                'BusinessShortCode' => config('services.mpesa.shortcode'),
                'Password' => $this->generatePassword(),
                'Timestamp' => now()->format('YmdHis'),
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $plan->price,
                'PartyA' => $customer->phone_number,
                'PartyB' => config('services.mpesa.shortcode'),
                'PhoneNumber' => $customer->phone_number,
                'CallBackURL' => route('api.v1.payments.mpesa.callback', ['tenant_id' => $customer->tenant_id]),
                'AccountReference' => 'Hotspot_' . $customer->id,
                'TransactionDesc' => "Payment for {$plan->name}"
            ]);

            if ($response->successful()) {
                // Update with the REAL CheckoutRequestID from Safaricom
                $realId = $response->json('CheckoutRequestID');
                $transaction->update(['checkout_request_id' => $realId]);

                return ['success' => true, 'checkout_id' => $realId];
            }

            return ['success' => false, 'message' => 'Safaricom API Rejected the request.'];

        } catch (\Exception $e) {
            Log::error("M-Pesa Initiation Failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Technical error occurred.'];
        }
    }

    private function generateToken() { /* Logic for OAuth token */ return 'token'; }
    private function generatePassword() { /* Base64 Logic */ return 'pass'; }
}