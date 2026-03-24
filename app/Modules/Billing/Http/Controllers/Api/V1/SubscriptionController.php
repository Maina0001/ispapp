<?php

namespace Modules\Billing\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Billing\Http\Requests\StoreSubscriptionRequest;
use Modules\Billing\Http\Resources\SubscriptionResource;
use Modules\Billing\Models\Subscription;
use Modules\Billing\Services\BillingService;
use Illuminate\Http\JsonResponse;

/**
 * Class SubscriptionController
 */
class SubscriptionController extends Controller
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    /**
     * Get paginated subscriptions.
     */
    public function index(): AnonymousResourceCollection
    {
        $subscriptions = Subscription::query()
            ->with(['customer', 'plan'])
            ->paginate(request()->query('per_page', 15));

        return SubscriptionResource::collection($subscriptions);
    }

    /**
     * Create a new subscription (starts the billing cycle).
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->billingService->createSubscription($request->validated());

        return (new SubscriptionResource($subscription))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Cancel a subscription. 
     * Note: This usually triggers a ServiceDeprovisioned event via the service.
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        $this->billingService->cancelSubscription($subscription);

        return response()->json(['message' => 'Subscription cancelled successfully.']);
    }
}