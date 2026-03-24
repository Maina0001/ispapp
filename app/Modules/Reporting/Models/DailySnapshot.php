<?php

namespace App\Modules\Reporting\Models;

use App\Core\Abstract\BaseModel;

class DailySnapshot extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'snapshot_date',
        'total_revenue',
        'total_invoiced',
        'new_subscriptions',
        'cancelled_subscriptions',
        'active_users_count',
        'total_data_usage_gb',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'total_revenue' => 'decimal:2',
        'total_invoiced' => 'decimal:2',
    ];
}