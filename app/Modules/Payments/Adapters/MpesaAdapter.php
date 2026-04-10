<?php

namespace App\Modules\Payments\Adapters;

use App\Modules\Payments\Interfaces\PaymentGatewayInterface;
use App\Modules\Payments\Services\MpesaService;

class MpesaAdapter implements PaymentGatewayInterface
{
    public function __construct(protected MpesaService $mpesaService) {}

    public function requestPayment(float $amount, string $phone, array $metadata = []): array
    {
        // Encapsulate the STK Push logic here
        return $this->mpesaService->stkPush($phone, $amount, $metadata['reference']);
    }

    public function formatCallbackData(array $rawData): array
    {
        // Transform Safaricom's nested JSON into a flat, standard Payment DTO
        return [
            'external_id' => data_get($rawData, 'Body.stkCallback.CheckoutRequestID'),
            'amount' => data_get($rawData, 'Body.stkCallback.CallbackMetadata.Item.0.Value'),
            'status' => data_get($rawData, 'Body.stkCallback.ResultCode') === 0 ? 'success' : 'failed',
            'phone' => data_get($rawData, 'Body.stkCallback.CallbackMetadata.Item.4.Value'),
        ];
    }
}