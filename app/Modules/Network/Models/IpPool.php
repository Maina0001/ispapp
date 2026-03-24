<?php

namespace App\Modules\Network\Models;

use App\Core\Abstract\BaseModel;

class IpPool extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'name',
        'start_ip',
        'end_ip',
        'gateway',
        'dns_servers',
        'type', // 'pppoe', 'dhcp', 'static'
        'is_for_suspended', // If true, users in this pool see a 'Pay Your Bill' page
    ];

    protected $casts = [
        'dns_servers' => 'array',
        'is_for_suspended' => 'boolean',
    ];
}