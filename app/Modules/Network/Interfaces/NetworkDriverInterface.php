<?php

namespace Modules\Network\Interfaces;

interface NetworkDriverInterface
{
    public function authenticateUser(string $identity, array $options = []): bool;

    public function suspendUser(string $identity): bool;

    public function resumeUser(string $identity): bool;

    public function removeUser(string $identity): bool;

    public function updateBandwidth(string $identity, string $profileName): bool;

    public function getActiveSession(string $identity): ?array;

    public function disconnectUser(string $identity): void;
}