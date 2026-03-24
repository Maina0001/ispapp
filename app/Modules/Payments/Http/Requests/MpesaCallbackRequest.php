<?php

namespace Modules\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class MpesaCallbackRequest
 * Validates the structure of the Safaricom M-Pesa STK Push callback.
 * Designed to handle the nested JSON structure of the Daraja API response.
 */
class MpesaCallbackRequest extends FormRequest
{
    /**
     * Determine if the request is authorized.
     * Webhooks are typically authorized via IP whitelisting or secure URL tokens.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the M-Pesa JSON payload.
     * @return array
     */
    public function rules(): array
    {
        return [
            'Body' => ['required', 'array'],
            'Body.stkCallback' => ['required', 'array'],
            'Body.stkCallback.MerchantRequestID' => ['required', 'string'],
            'Body.stkCallback.CheckoutRequestID' => ['required', 'string'],
            'Body.stkCallback.ResultCode'        => ['required', 'integer'],
            'Body.stkCallback.ResultDesc'        => ['required', 'string'],
            'Body.stkCallback.CallbackMetadata'  => ['nullable', 'array'],
            'Body.stkCallback.CallbackMetadata.Item' => ['required_if:Body.stkCallback.ResultCode,0', 'array'],
        ];
    }

    /**
     * Custom messages for webhook logging.
     * @return array
     */
    public function messages(): array
    {
        return [
            'Body.stkCallback.CheckoutRequestID.required' => 'M-Pesa Callback missing CheckoutRequestID.',
            'Body.required' => 'Invalid M-Pesa payload structure.',
        ];
    }
}