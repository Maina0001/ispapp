<?php

namespace Modules\Network\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SuspendRadiusRequest
 * Validates the parameters to disconnect and block a subscriber.
 */
class SuspendRadiusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-network-access');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reason' => strip_tags(trim($this->reason ?? 'Administrative Suspension')),
        ]);
    }

    public function rules(): array
    {
        $tenantId = app(\App\Core\TenantContext::class)->getTenantId();

        return [
            'username' => [
                'required',
                'string',
                // Ensure the account belongs to the current tenant
                Rule::exists('radius_accounts', 'username')->where('tenant_id', $tenantId)
            ],
            'reason'                 => ['required', 'string', 'max:255'],
            'disconnect_immediately' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.exists' => 'The subscriber account was not found in your network scope.',
        ];
    }
}