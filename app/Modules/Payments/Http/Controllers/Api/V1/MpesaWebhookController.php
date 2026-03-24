<?php

namespace Modules\Payments\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payments\Http\Requests\MpesaCallbackRequest;
use Modules\Payments\Jobs\ProcessMpesaCallback;
use Modules\Payments\Models\MpesaTransaction;
use Illuminate\Support\Facades\Log;

/**
 * Handles incoming callbacks from Safaricom Daraja API.
 */
class MpesaWebhookController extends Controller
{
    /**
     * Receive and persist the M-Pesa callback data.
     * * @param MpesaCallbackRequest $request
     * @return JsonResponse
     */
    public function handleCallback(MpesaCallbackRequest $request): JsonResponse
    {
        // 1. Log raw input for audit trail (Critical for payment disputes)
        Log::channel('mpesa')->info('Callback Received', $request->all());

        // 2. Persist the raw transaction state
        // tenant_id is resolved via the 'CheckoutRequestID' mapping or URL parameter
        $transaction = MpesaTransaction::create([
            'merchant_request_id' => $request->input('Body.stkCallback.MerchantRequestID'),
            'checkout_request_id' => $request->input('Body.stkCallback.CheckoutRequestID'),
            'raw_payload'         => $request->all(),
            'status'              => 'received',
            'tenant_id'           => $request->route('tenant_id'), 
        ]);

        // 3. Dispatch Job for heavy lifting (Validation, Ledger Entry, Service Restoration)
        ProcessMpesaCallback::dispatch($transaction)
            ->onQueue('payments');

        // 4. Respond to Safaricom immediately to prevent retries
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ], 200);
    }
}