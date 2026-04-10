<?php

namespace Modules\Customer\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Models\MpesaTransaction;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    /**
     * GET /api/v1/customer/payment-status/{checkoutId}
     * * Polled by the frontend JS every 3-5 seconds.
     */
    public function checkPayment(string $checkoutId): JsonResponse
    {
        // 1. Find the transaction by Safaricom's CheckoutRequestID
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutId)->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Transaction record not found.'
            ], 404);
        }

        // 2. Return the status
        // The 'completed' status is set by our Webhook Listener we built earlier
        return response()->json([
            'status' => $transaction->status, // 'pending', 'completed', or 'failed'
            'is_active' => ($transaction->status === 'completed'),
            'message' => $this->getStatusMessage($transaction->status)
        ]);
    }

    /**
     * Helper to return user-friendly messages for the portal
     */
    private function getStatusMessage(string $status): string
    {
        return match ($status) {
            'completed' => 'Payment successful! Redirecting to internet...',
            'failed'    => 'Payment failed or was cancelled. Please try again.',
            default     => 'Waiting for PIN entry on your phone...',
        };
    }
}