<?php

namespace App\Core\Context;

use Illuminate\Database\Eloquent\Model;

/**
 * Enterprise Singleton to manage the state of the current Tenant across the application lifecycle.
 */
class TenantContext
{
    private ?int $tenantId = null;
    private ?Model $tenant = null;

    /**
     * Sets the active tenant. This is usually called by the IdentifyTenant middleware.
     */
    public function setTenant(Model $tenant): void
    {
        $this->tenant = $tenant;
        $this->tenantId = (int) $tenant->getKey();
    }

    /**
     * Retrieve the current Tenant ID.
     */
    public function getTenantId(): ?int
    {
        return $this->tenantId;
    }

    /**
     * Retrieve the full Tenant Model (e.g., for accessing settings/branding).
     */
    public function getTenant(): ?Model
    {
        return $this->tenant;
    }

    /**
     * Boolean check for tenant presence.
     */
    public function hasTenant(): bool
    {
        return $this->tenantId !== null;
    }

    /**
     * Reset the context (useful for testing or CLI jobs processing multiple tenants).
     */
    public function reset(): void
    {
        $this->tenant = null;
        $this->tenantId = null;
    }
}