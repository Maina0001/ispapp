<?php

namespace Modules\Customer\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Customer\Http\Requests\StoreCustomerRequest;
use Modules\Customer\Http\Requests\UpdateCustomerRequest;
use Modules\Customer\Http\Resources\CustomerResource;
use Modules\Customer\Models\Customer;
use Modules\Customer\Services\CustomerService;
use Modules\Customer\Services\OnboardingService;

/**
 * Class CustomerController
 * @package Modules\Customer\Http\Controllers\Api\V1
 */
class CustomerController extends Controller
{
    /**
     * @param CustomerService $customerService
     * @param OnboardingService $onboardingService
     */
    public function __construct(
        protected CustomerService $customerService,
        protected OnboardingService $onboardingService
    ) {}

    /**
     * List customers with pagination.
     * Respects tenant_id via BaseModel global scope.
     */
    public function index(): AnonymousResourceCollection
    {
        $customers = Customer::query()
            ->with(['subscriptions', 'activeService'])
            ->paginate(request()->query('per_page', 15));

        return CustomerResource::collection($customers);
    }

    /**
     * Register a new customer and trigger onboarding.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        // Delegate complex registration & job dispatching to the service
        $customer = $this->onboardingService->registerNewCustomer($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get specific customer details.
     */
    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer->load(['subscriptions', 'radiusAccount']));
    }

    /**
     * Update customer profile.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $updatedCustomer = $this->customerService->updateProfile($customer, $request->validated());

        return new CustomerResource($updatedCustomer);
    }

    /**
     * Delete/Soft-delete a customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->deleteCustomer($customer);

        return response()->json([
            'message' => 'Customer successfully removed from system.'
        ], 200);
    }
}