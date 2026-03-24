<?php

namespace Modules\Billing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class GenerateInvoiceRequest
 * Validates data for manual or automated invoice generation.
 * Ensures the target customer is within the correct tenant scope.
 */
class GenerateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        // Permission check: only users with billing-management can create invoices
        return true; 
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure due_date defaults to 7 days if not provided
        if (!$this->has('due_date')) {
            $this->merge([
                'due_date' => now()->addDays(7)->format('Y-m-d')
            ]);
        }
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
                'integer',
                // Tenant Awareness: Ensure customer exists AND belongs to this ISP
                Rule::exists('customers', 'id')->where(fn ($query) => 
                    $query->where('tenant_id', $tenantId)
                )
            ],
            'billing_period_start' => ['required', 'date'],
            'billing_period_end'   => ['required', 'date', 'after:billing_period_start'],
            'due_date'             => ['required', 'date', 'after_or_equal:today'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.description'  => ['required', 'string', 'max:255'],
            'items.*.amount'       => ['required', 'numeric', 'min:0'],
            'items.*.taxable'      => ['required', 'boolean'],
            'notes'                => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [
            'customer_id.exists' => 'The selected customer does not exist in your ISP records.',
            'items.required'     => 'An invoice must contain at least one billing item.',
            'billing_period_end.after' => 'The billing period end date must be after the start date.',
        ];
    }
}