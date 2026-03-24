<?php

namespace App\Modules\Reporting\Models;

use App\Core\Abstract\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'event',       // 'created', 'updated', 'deleted', 'login'
        'auditable_type', 
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}