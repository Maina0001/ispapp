<?php

namespace App\Modules\Customer\Models;

use App\Core\Abstract\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNote extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'user_id',    // The admin/staff who wrote the note
        'content',
        'type',       // 'support', 'billing', 'technical'
        'is_critical' // To highlight important info (e.g., "Aggressive dog on site")
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}