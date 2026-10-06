<?php

namespace Modules\Payments\Models;

use App\Core\Abstract\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends BaseModel
{
    protected $table = 'mpesa_transactions';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'plan_id',
        'phone',
        'amount',
        'merchant_request_id',
        'checkout_request_id',
        'mpesa_receipt_number',
        'result_code',
        'result_desc',
        'transaction_date',
        'status',       // 'pending' | 'completed' | 'failed'
        'raw_payload',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'raw_payload'      => 'array',
        'transaction_date' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relations
    // ------------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Models\Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(\Modules\Network\Models\ServicePlan::class, 'plan_id');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeByCheckoutId($query, string $id)
    {
        return $query->where('checkout_request_id', $id);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}