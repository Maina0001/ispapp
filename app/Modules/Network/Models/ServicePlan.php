<?php

namespace Modules\Network\Models;

use App\Core\Abstract\BaseModel;

class ServicePlan extends BaseModel
{
    protected $fillable = [
        'tenant_id', 
        'name', 
        'duration_minutes', 
        'price', 
        'bandwidth_limit'
    ];
}