<?php

namespace App\Modules\Payments\Services;

use App\Modules\Payments\Jobs\ProcessMpesaCallback;
use App\Modules\Payments\Models\MpesaTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class MpesaService implements PaymentGatewayInterface
{
    /**
     * Generate an OAuth access token from Safaricom.
     *
     * Tokens are valid for ~1 hour. In production, cache this.
     */
    protected function getOAuthToken(): string
    {
        $response = Http::timeout(config('mpesa.timeout'))
            ->withBasicAuth(config('mpesa.consumer_key'), config('mpesa.consumer_secret'))
            ->get(config('mpesa.base_url') . '/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            Log::error('M-Pesa OAuth token request failed.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new RuntimeException('Could not authenticate with M-Pesa.');
        }

        $token = $response->json('access_token');

        if (! $token) {
            throw new RuntimeException('M-Pesa returned no access token.');
        }

        return $token;
    }

    /**
     * STK push password = Base64(Shortcode + Passkey + Timestamp).
     */
    protected function generatePassword(string $timestamp): string
    {
        return base64_encode(
            config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp
        );
    }

    /**
     * Normalize a Kenyan phone number to 2547XXXXXXXX or 2541XXXXXXXX.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // strip non-digits

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        } elseif (str_starts_with($phone, '+254')) {
            $phone = substr($phone, 1);
        }

        if (! preg_match('/^254(7|1)\d{8}$/', $phone)) {
            throw new RuntimeException("Invalid Kenyan phone number: {$phone}");
        }

        return $phone;
    }

    /**
     * Trigger M-Pesa STK Push (Lipa Na M-Pesa Online).
     *
     * @throws RuntimeException on any failure.
     */
    public function stkPush(array $details): array
    {
        $timestamp = now()->format('YmdHis');
        $phone     = $this->normalizePhone($details['phone']);

        $payload = [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password'          => $this->generatePassword($timestamp),
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) round($details['amount']),
            'PartyA'            => $phone,
            'PartyB'            => config('mpesa.shortcode'),
            'PhoneNumber'       => $phone,
            'CallBackURL'       => config('mpesa.callback_url'),
            'AccountReference'  => $details['account_ref'] ?? ('ISP-' . ($details['customer_id'] ?? 'NA')),
            'TransactionDesc'   => $details['description'] ?? 'Internet subscription',
        ];

        Log::info('Initiating M-Pesa STK Push.', [
            'customer_id' => $details['customer_id'] ?? null,
            'phone'       => $phone,
            'amount'      => $payload['Amount'],
        ]);

        try {
            $response = Http::timeout(config('mpesa.timeout'))
                ->withToken($this->getOAuthToken())
                ->post(config('mpesa.base_url') . '/mpesa/stkpush/v1/processrequest', $payload);
        } catch (Throwable $e) {
            Log::error('M-Pesa STK Push HTTP error.', ['error' => $e->getMessage()]);
            throw new RuntimeException('Failed to reach M-Pesa. Please try again.');
        }

        $data = $response->json();

        if ($response->failed() || (($data['ResponseCode'] ?? null) !== '0')) {
            Log::error('M-Pesa STK Push rejected.', [
                'status'   => $response->status(),
                'response' => $data,
            ]);
            throw new RuntimeException(
                $data['errorMessage']
                ?? $data['CustomerMessage']
                ?? 'M-Pesa rejected the request.'
            );
        }

        // Persist BEFORE returning so the callback can match this record.
        MpesaTransaction::create([
            'tenant_id'           => $details['tenant_id'],
            'customer_id'         => $details['customer_id'],
            'phone'               => $phone,
            'amount'              => $payload['Amount'],
            'merchant_request_id' => $data['MerchantRequestID'] ?? null,
            'checkout_request_id' => $data['CheckoutRequestID'] ?? null,
            'status'              => 'pending',
        ]);

        return $data;
    }

    /**
     * Query Safaricom for the status of a checkout request.
     */
    public function verifyTransaction(string $checkoutRequestId): bool
    {
        $timestamp = now()->format('YmdHis');

        try {
            $response = Http::timeout(config('mpesa.timeout'))
                ->withToken($this->getOAuthToken())
                ->post(config('mpesa.base_url') . '/mpesa/stkpushquery/v1/query', [
                    'BusinessShortCode' => config('mpesa.shortcode'),
                    'Password'          => $this->generatePassword($timestamp),
                    'Timestamp'         => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ]);
        } catch (Throwable $e) {
            Log::error('M-Pesa verify HTTP error.', [
                'checkout_request_id' => $checkoutRequestId,
                'error'               => $e->getMessage(),
            ]);
            return false;
        }

        $data = $response->json();

        Log::info('M-Pesa verify response.', [
            'checkout_request_id' => $checkoutRequestId,
            'result_code'         => $data['ResultCode'] ?? null,
        ]);

        return (int) ($data['ResultCode'] ?? -1) === 0;
    }

    /**
     * Dispatch the callback payload to a queued job.
     */
    public function processCallback(array $data): void
    {
        ProcessMpesaCallback::dispatch($data)->onQueue('payments');
    }

    public function charge(array $details): array
    {
        return $this->stkPush($details);
    }

    public function refund(string $id, float $amt): bool
    {
        // STK Push does not support refunds. Use B2C API for refunds.
        return false;
    }
}