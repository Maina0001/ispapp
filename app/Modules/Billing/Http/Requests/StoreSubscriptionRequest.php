<?php

namespace Modules\Billing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreSubscriptionRequest
 * Validates the creation of a new recurring service subscription.
 */
class StoreSubscriptionRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        $tenantId = $this->header('X-Tenant-ID');

        return [
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')->where(fn ($query) => 
                    $query->where('tenant_id', $tenantId)
                )
            ],
            'plan_id' => [
                'required',
                // Ensure the bandwidth plan is offered by this tenant
                Rule::exists('bandwidth_profiles', 'id')->where(fn ($query) => 
                    $query->where('tenant_id', $tenantId)
                )
            ],
            'billing_cycle' => ['required', 'in:monthly,quarterly,annually'],
            'start_date'    => ['required', 'date', 'after_or_equal:today'],
            'auto_renew'    => ['boolean'],
            'discount_code' => ['nullable', 'string', 'exists:discounts,code'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'plan_id.exists' => 'The selected service plan is not available for your ISP.',
            'billing_cycle.in' => 'Please select a valid billing cycle (Monthly, Quarterly, or Annually).',
        ];
    }
}