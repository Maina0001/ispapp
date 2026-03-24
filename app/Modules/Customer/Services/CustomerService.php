<?php

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\Customer;
use App\Core\Abstract\BaseService;
use Illuminate\Support\Facades\DB;

class CustomerService extends BaseService
{
    /**
     * Create a new customer and handle initial state.
     */
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'first_name'   => $data['first_name'],
                'last_name'    => $data['last_name'],
                'email'        => $data['email'],
                'phone_number' => $this->formatPhoneNumber($data['phone_number']),
                'id_number'    => $data['id_number'],
                'latitude'     => $data['latitude'] ?? null,
                'longitude'    => $data['longitude'] ?? null,
                'status'       => 'lead', // Start as lead until service is provisioned
            ]);

            // Logic for internal notifications or CRM syncing goes here
            
            return $customer;
        });
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Standardize to E.164 (e.g., +254 for Kenya)
        return preg_replace('/^0/', '254', $phone);
    }
}