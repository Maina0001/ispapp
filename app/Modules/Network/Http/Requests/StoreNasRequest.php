<?php

namespace Modules\Network\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreNasRequest
 * Validates the registration of a Network Access Server (NAS) device.
 */
class StoreNasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to register hardware.
     */
    public function authorize(): bool
    {
        // Only users with network-admin permissions for this tenant
        return $this->user()->can('manage-network-infrastructure');
    }

    /**
     * Prepare technical strings for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'shortname' => strtolower(trim($this->shortname)),
            'nasname'   => trim($this->nasname), // The IP address
        ]);
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        $tenantId = app(\App\Core\TenantContext::class)->getTenantId();

        return [
            'nasname' => [
                'required',
                'ip',
                // Ensure IP is unique only within this tenant's network
                Rule::unique('nas', 'nasname')->where('tenant_id', $tenantId)
            ],
            'shortname'   => ['required', 'string', 'max:32', 'alpha_dash'],
            'type'        => ['required', 'string', 'in:mikrotik,cisco,chillispot,other'],
            'secret'      => ['required', 'string', 'min:8', 'max:64'],
            'description' => ['nullable', 'string', 'max:255'],
            'ports'       => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'nasname.unique' => 'A router with this IP is already registered in your tenant.',
            'secret.min'     => 'The RADIUS shared secret must be at least 8 characters.',
        ];
    }
}