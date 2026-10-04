<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Customer\Models\Customer;
use Modules\Network\Models\ServicePlan;

class Tenant extends Model
{
    /**
     * The attributes that are mass assignable.
     * These must match the columns in your 2019_..._create_tenants_table migration.
     */
    protected $fillable = [
        'name',
        'slug',
        'api_key',
        'is_active',
        'settings' // Optional: for storing ISP-specific configs like M-Pesa credentials
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    /**
     * Relationship: A Tenant (ISP) has many Customers.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Relationship: A Tenant (ISP) offers many Service Plans.
     */
    public function servicePlans(): HasMany
    {
        return $this->hasMany(ServicePlan::class);
    }

    /**
     * Route Key Name
     * Allows you to find the ISP by slug in the URL (e.g., /portal/kariuki-isp)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
