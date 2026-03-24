<?php

namespace Modules\Network\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreIpPoolRequest
 * Validates IP address blocks for DHCP/Static allocation.
 */
class StoreIpPoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-network-infrastructure');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'subnet' => [
                'required',
                'string',
                // Regex for CIDR notation (e.g., 192.168.10.0/24)
                'regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}\/[0-9]{1,2}$/'
            ],
            'gateway'       => ['required', 'ip'],
            'dns_primary'   => ['required', 'ip'],
            'dns_secondary' => ['nullable', 'ip'],
            'pool_type'     => ['required', 'in:static,dynamic,cgnat'],
        ];
    }

    public function messages(): array
    {
        return [
            'subnet.regex' => 'Please provide a valid CIDR subnet (e.g., 10.50.0.0/22).',
        ];
    }
}