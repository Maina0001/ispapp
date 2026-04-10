<?php

namespace App\Core\Services;

use App\Core\Context\TenantContext;
use App\Core\Abstract\BaseService;

class SettingsService extends BaseService
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Get a setting specific to the current tenant.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->tenantContext->hasTenant()) {
            return config("defaults.$key", $default);
        }

        // Logic to fetch from a 'settings' table filtered by tenant_id
        return $this->tenantContext->getTenant()->settings()->where('key', $key)->value('value') ?? $default;
    }
}