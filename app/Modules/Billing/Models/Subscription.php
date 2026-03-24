<?php

namespace App\Modules\Billing\Models;

use App\Core\Abstract\BaseModel;
use App\Modules\Customer\Models\Customer;
use App\Modules\Network\Models\ServicePlan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'service_plan_id',
        'status', // 'active', 'suspended', 'expired', 'pending'
        'billing_cycle', // 'monthly', 'quarterly'
        'starts_at',
        'expires_at',
        'suspended_at',
        'last_billed_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'suspended_at' => 'datetime',
        'last_billed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ServicePlan::class, 'service_plan_id');
    }
}