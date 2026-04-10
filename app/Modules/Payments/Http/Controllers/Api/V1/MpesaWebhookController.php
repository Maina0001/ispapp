<?php

namespace Modules\Payments\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Payments\Http\Requests\MpesaCallbackRequest;
use Modules\Payments\Jobs\ProcessMpesaCallback;
use Modules\Payments\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class MpesaWebhookController extends Controller
{
    /**
     * We inject the Interface, not the Model.
     * This is the "D" in SOLID (Dependency Inversion).
     */
    public function __construct(
        protected PaymentRepositoryInterface $repository
    ) {}

    public function handleCallback(MpesaCallbackRequest $request): JsonResponse
    {
        // 1. Domain Logging (Keep it specific to the payment channel)
        Log::channel('mpesa')->info('M-Pesa Webhook Hit', [
            'checkout_id' => $request->input('Body.stkCallback.CheckoutRequestID')
        ]);

        // 2. Persist via Repository
        // The repository handles mapping the raw JSON to the database columns
        $transaction = $this->repository->storeRawCallback(
            $request->all(), 
            $request->route('tenant_id') // Pass if not handled by middleware
        );

        // 3. Dispatch the Job
        // We pass only the ID or the Model to keep the queue payload light
        ProcessMpesaCallback::dispatch($transaction)
            ->onQueue('payments');

        // 4. Immediate Safaricom Response (The "Contract" with Daraja)
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ], 200);
    }
}