<?php

namespace Modules\Customer\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Models\Customer;
use Modules\Customer\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerStatusController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Manually suspend a customer service.
     */
    public function suspend(Request $request, Customer $customer): JsonResponse
    {
        $request->validate(['reason' => 'required|string']);

        $this->customerService->suspendCustomer($customer, $request->reason);

        return response()->json(['message' => 'Customer access suspended.']);
    }

    /**
     * Reactivate a customer service.
     */
    public function reactivate(Customer $customer): JsonResponse
    {
        $this->customerService->reactivateCustomer($customer);

        return response()->json(['message' => 'Customer access restored.']);
    }
}