<?php

namespace Modules\Customer\Models;

use App\Core\Abstract\BaseModel;
use Modules\Billing\Models\Subscription;
use Modules\Billing\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone_number',
        'id_number',      // National ID or Passport
        'address',
        'latitude',       // For installation mapping
        'longitude',
        'status',
        'mac_address',
        'service_expiry_at',
        'last_payment_at', // 'active', 'inactive', 'lead'
        'billing_type',   // 'prepaid', 'postpaid'
    ];

    public function getFullNameAttribute(): string
    {
        return $this->name;
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