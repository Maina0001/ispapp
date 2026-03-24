<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCustomerRequest
 */
class UpdateCustomerRequest extends FormRequest
{
    /**
     * Authorization logic.
     */
    public function authorize(): bool
    {
        // Future Multi-tenant: Verify that the customer being updated belongs to the active tenant
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $customerId = $this->route('customer')->id;
        $tenantId = $this->header('X-Tenant-ID');

        return [
            'first_name'   => ['sometimes', 'string', 'max:50'],
            'last_name'    => ['sometimes', 'string', 'max:50'],
            'email'        => [
                'sometimes', 
                'email', 
                Rule::unique('customers')
                    ->ignore($customerId)
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
            ],
            'phone_number' => ['sometimes', 'string', 'min:10'],
            'status'       => ['sometimes', 'in:active,suspended,inactive'],
            'billing_type' => ['sometimes', 'in:prepaid,postpaid'],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The status must be either active, suspended, or inactive.',
        ];
    }
}