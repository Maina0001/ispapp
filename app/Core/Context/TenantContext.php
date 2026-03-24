<?php

namespace App\Core\Context;

class TenantContext
{
    private ?int $tenantId = null;
    private ?object $tenant = null;

    /**
     * Set the current active tenant.
     */
    public function setTenant(object $tenant): void
    {
        $this->tenant = $tenant;
        $this->tenantId = $tenant->id;
    }

    /**
     * Get the ID of the current tenant.
     */
    public function getTenantId(): ?int
    {
        return $this->tenantId;
    }

    /**
     * Get the full tenant model instance.
     */
    public function getTenant(): ?object
    {
        return $this->tenant;
    }

    /**
     * Check if a tenant has been identified.
     */
    public function hasTenant(): bool
    {
        return !is_null($this->tenantId);
    }
}