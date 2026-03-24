<?php

namespace App\Modules\Network\Models;

use App\Core\Abstract\BaseModel;
use App\Modules\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiusAccount extends BaseModel
{
    // FreeRADIUS default table for credentials
    protected $table = 'radcheck'; 
    public $timestamps = false; // radcheck usually doesn't have created_at/updated_at

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'username',      // The PPPoE Username
        'attribute',     // Usually 'Cleartext-Password'
        'op',            // Usually ':='
        'value',         // The Password
        'is_active',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Scope to find the account by username (used by RadiusManager)
     */
    public function scopeByUsername($query, string $username)
    {
        return $query->where('username', $username);
    }
}