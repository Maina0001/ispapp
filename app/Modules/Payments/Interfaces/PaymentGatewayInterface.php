<?php

namespace App\Modules\Payments\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * Trigger a request for payment (e.g., STK Push).
     */
    public function requestPayment(float $amount, string $identifier, array $metadata = []): array;

    /**
     * Standardize the raw gateway callback into a format the system understands.
     */
    public function formatCallbackData(array $rawData): array;
}