<?php

namespace Modules\Customer\Models;

use App\Core\Abstract\BaseModel;
use Modules\Billing\Models\Subscription;
use Modules\Billing\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function payments(): HasMany
    {
        return $this->hasMany(\Modules\Payments\Models\Payment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(\Modules\Billing\Models\Subscription::class, 'customer_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\Modules\Billing\Models\Invoice::class);
    }
    public function hasOverdueInvoices(): bool
    {
        return $this->invoices()
        ->where('status', '!=', 'paid')
        ->where('due_date', '<', now())
        ->exists();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }
     public function radiusAccount(): HasOne
    {
        return $this->hasOne(\Modules\Network\Models\RadiusAccount::class, 'username', 'mac_address');
    }
        public function currentSubscription(): ?\Modules\Billing\Models\Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();
    }



}