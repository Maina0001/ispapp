<?php

namespace App\Modules\Network\Services;

use App\Modules\Network\Models\RadiusAccount;
use App\Modules\Customer\Models\Customer;
use App\Modules\Network\Events\RadiusAccountCreated;
use App\Modules\Network\Events\RadiusAccountUpdated;

class RadiusManager
{
    public function __construct(protected FreeRadiusAdapter $adapter) {}

    /**
     * Creates a RADIUS account using the MAC address as the identity.
     */
    public function createRadiusAccount(Customer $customer, string $password): RadiusAccount
    {
        // For Hotspots, username MUST be the MAC address
        // We pass the tenant_id so the adapter can scope the SQL record
        $account = $this->adapter->createUser(
            $customer->mac_address, 
            $password, 
            $customer->tenant_id
        );
        
        event(new RadiusAccountCreated($account));
        
        return $account;
    }

    public function suspendRadiusAccount(RadiusAccount $account): void
    {
        // We use the adapter to flip the 'Auth-Type' to 'Reject' 
        // or change the password to prevent login.
        $this->adapter->updateUser($account, [
            'is_active' => false,
            'tenant_id' => $account->tenant_id
        ]);
        
        event(new RadiusAccountUpdated($account));
    }

    public function resumeRadiusAccount(RadiusAccount $account): void
    {
        $this->adapter->updateUser($account, [
            'is_active' => true,
            'tenant_id' => $account->tenant_id
        ]);
        
        event(new RadiusAccountUpdated($account));
    }
}