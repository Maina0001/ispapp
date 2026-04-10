<?php

namespace Modules\Customer\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Services\OnboardingService;
use Modules\Network\Models\ServicePlan;
use Modules\Payments\Interfaces\PaymentInitiatorInterface;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    /**
     * Note: We inject the INTERFACE for payments, not the Mpesa Service directly.
     */
    public function __construct(
        protected OnboardingService $onboardingService,
        protected PaymentInitiatorInterface $paymentInitiator
    ) {}

    /**
     * POST /api/v1/customer/onboard
     * * The portal hits this endpoint when the user submits their phone number.
     */
    public function onboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mac'     => 'required|string',
            'phone'   => 'required|string|regex:/^254[0-9]{9}$/',
            'plan_id' => 'required|exists:service_plans,id',
        ]);

        try {
            // 1. Identify/Create Customer and bind the phone number
            $customer = $this->onboardingService->onboardByMac($validated['mac']);
            $customer->update(['phone_number' => $validated['phone']]);

            // 2. Resolve the requested package
            $plan = ServicePlan::find($validated['plan_id']);

            // 3. Initiate the Payment (STK Push)
            // This calls MpesaInitiationService via the interface
            $payment = $this->paymentInitiator->initiatePush($customer, $plan);

            if (!$payment['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $payment['message'] ?? 'Payment initiation failed.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'checkout_id' => $payment['checkout_id'],
                'message' => 'STK Push sent. Please enter your PIN on your phone.'
            ]);

        } catch (\Exception $e) {
            Log::error("API Onboarding Error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'A server error occurred during onboarding.'
            ], 500);
        }
    }
}