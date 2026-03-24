<?php

namespace App\Modules\Payments\Services;

interface PaymentGatewayInterface
{
    /**
     * Trigger a collection request (e.g., STK Push).
     */
    public function charge(array $details): array;

    /**
     * Process a refund for a transaction.
     */
    public function refund(string $transactionId, float $amount): bool;
}