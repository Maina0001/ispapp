<?php

namespace Modules\Payments\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Payments\Http\Requests\InitiatePaymentRequest;
use Modules\Payments\Http\Resources\PaymentResource;
use Modules\Payments\Models\Payment;
use Modules\Payments\Services\MpesaService;
use Illuminate\Http\JsonResponse;

/**
 * Class PaymentController
 */
class PaymentController extends Controller
{
    public function __construct(
        protected MpesaService $mpesaService
    ) {}

    /**
     * Display a paginated list of payments.
     * Scoped by tenant_id via BaseModel.
     */
    public function index(): AnonymousResourceCollection
    {
        $payments = Payment::query()
            ->with(['customer', 'invoice'])
            ->latest()
            ->paginate(request()->query('per_page', 15));

        return PaymentResource::collection($payments);
    }

    /**
     * Initiate an M-Pesa STK Push.
     */
    public function store(InitiatePaymentRequest $request): JsonResponse
    {
        // Delegate to MpesaService to handle Token generation and API request
        $response = $this->mpesaService->initiateStkPush(
            $request->amount,
            $request->phone_number,
            $request->reference
        );

        return response()->json([
            'message' => 'STK Push sent to handset.',
            'data'    => $product_id // Transaction reference
        ], 201);
    }

    /**
     * Get specific payment details.
     */
    public function show(Payment $payment): PaymentResource
    {
        return new PaymentResource($payment);
    }
}