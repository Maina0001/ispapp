<?php

namespace Modules\Billing\Models;

use App\Core\Abstract\BaseModel;
use Modules\Customer\Models\Customer;
use Modules\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Invoice extends BaseModel
{
    protected $table = 'invoices';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'invoice_number',
        'amount',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'balance',
        'status', 
        'due_date', // e.g., 'unpaid', 'partial', 'paid', 'cancelled'
        'due_at',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): BelongsToMany
    {
        // Many-to-many because one payment could clear multiple invoices (enterprise reconciliation)
        return $this->belongsToMany(Payment::class, 'invoice_payments')
                    ->withPivot('amount_applied')
                    ->withTimestamps();
    }
}