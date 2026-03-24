<?php

namespace App\Modules\Network\Services;

use App\Modules\Network\Models\RadiusAccount;

class FreeRadiusAdapter
{
    public function createUser(string $username, string $password, int $tenantId): RadiusAccount
    {
        return RadiusAccount::updateOrCreate(
            [
                'username' => $username,
                'attribute' => 'Cleartext-Password'
            ],
            [
                'op' => ':=',
                'value' => $password,
                'tenant_id' => $tenantId
            ]
        );
    }

    public function updateUser(RadiusAccount $account, array $data): void
    {
        if (isset($data['is_active']) && $data['is_active'] === false) {
            // Force rejection in RADIUS
            $account->update(['value' => 'DISABLED_BY_ADMIN_' . str_random(5)]);
        } else {
            // Restore password (assuming MAC-as-Password)
            $account->update(['value' => $account->username]);
        }
    }
}