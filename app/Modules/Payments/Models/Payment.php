<?php

namespace App\Modules\Payments\Models;

use App\Core\Abstract\BaseModel;
use App\Modules\Customer\Models\Customer;
use App\Modules\Billing\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'amount',
        'currency',
        'payment_method', // 'mpesa', 'bank', 'cash'
        'transaction_reference',
        'status',         // 'completed', 'pending', 'reversed'
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_payments')
                    ->withPivot('amount_applied')
                    ->withTimestamps();
    }
}