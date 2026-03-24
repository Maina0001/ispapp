<?php

namespace App\Modules\Network\Models;

use App\Core\Abstract\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nas extends BaseModel
{
    protected $table = 'nas'; // Standard FreeRADIUS table name

    protected $fillable = [
        'tenant_id',
        'shortname',    // Friendly name: "Main_Fiber_Router"
        'nasname',      // IP Address or FQDN
        'type',         // 'mikrotik', 'cisco', etc.
        'ports',
        'secret',       // RADIUS shared secret
        'api_username',
        'api_password',
        'api_port',
    ];

    protected $hidden = ['secret', 'api_password'];

    public function accounts(): HasMany
    {
        return $this->hasMany(RadiusAccount::class, 'nasipaddress', 'nasname');
    }
}