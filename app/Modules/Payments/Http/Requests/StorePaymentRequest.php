<?php

namespace Modules\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StorePaymentRequest
 * Validates data for initiating or manually recording a payment.
 * Ensures the phone number is M-Pesa ready and the invoice exists within the tenant.
 */
class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to record payments.
     * @return bool
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Prepare data for validation.
     * Normalizes the phone number to the 2547XXXXXXXX format required by Safaricom.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone_number')) {
            $phone = preg_replace('/[^0-9]/', '', $this->phone_number);
            
            if (str_starts_with($phone, '0')) {
                $phone = '254' . substr($phone, 1);
            } elseif (str_starts_with($phone, '+')) {
                $phone = substr($phone, 1);
            }

            $this->merge(['phone_number' => $phone]);
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
            'invoice_id' => [
                'required',
                Rule::exists('invoices', 'id')->where(fn ($query) => 
                    $query->where('tenant_id', $tenantId)
                )
            ],
            'amount' => ['required', 'numeric', 'min:1'],
            'phone_number' => ['required', 'string', 'regex:/^254[17][0-9]{8}$/'],
            'payment_method' => ['required', 'string', 'in:mpesa,bank_transfer,cash'],
            'reference' => [
                'nullable', 
                'string', 
                'max:50',
                Rule::unique('payments', 'reference')->where('tenant_id', $tenantId)
            ],
        ];
    }

    /**
     * Custom messages for validation.
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Please provide a valid Safaricom phone number (e.g., 254712345678).',
            'invoice_id.exists' => 'The selected invoice was not found in your tenant records.',
            'reference.unique' => 'This transaction reference has already been used.',
        ];
    }
}