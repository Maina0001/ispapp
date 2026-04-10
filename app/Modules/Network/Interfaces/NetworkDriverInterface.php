<?php

namespace App\Modules\Network\Interfaces;

/**
 * Enterprise contract for all network hardware integration.
 */
interface NetworkDriverInterface
{
    public function authenticateUser(string $identity, array $options = []): bool;
    
    public function suspendUser(string $identity): bool;
    
    public function updateBandwidth(string $identity, string $profileName): bool;

    public function getActiveSession(string $identity): ?array;
}