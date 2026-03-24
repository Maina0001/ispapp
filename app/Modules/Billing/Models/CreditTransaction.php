<?php

namespace App\Modules\Billing\Models;

use App\Core\Abstract\BaseModel;
use App\Modules\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransaction extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'amount',
        'type', // 'credit' (add money), 'debit' (use money)
        'source', // 'mpesa', 'manual_adjustment', 'refund'
        'description',
        'reference_id', // ID of the payment or invoice linked
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}