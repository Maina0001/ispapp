<?php

namespace App\Modules\Customer\Models;

use App\Core\Abstract\BaseModel;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Billing\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'id_number',      // National ID or Passport
        'address',
        'latitude',       // For installation mapping
        'longitude',
        'status',         // 'active', 'inactive', 'lead'
        'billing_type',   // 'prepaid', 'postpaid'
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }
}