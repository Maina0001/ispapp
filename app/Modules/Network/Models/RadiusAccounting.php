<?php

namespace App\Modules\Network\Models;

use App\Core\Abstract\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class RadiusAccounting extends BaseModel
{
    protected $table = 'radacct';
    protected $primaryKey = 'radacctid';
    public $timestamps = false;

    // These fields are populated by FreeRADIUS, not Laravel
    protected $guarded = [];

    /**
     * Calculate total data used in GB
     */
    public function getTotalGbAttribute(): float
    {
        // (Input + Output Octets) / 1024^3
        return round(($this->acctinputoctets + $this->acctoutputoctets) / 1073741824, 2);
    }

    /**
     * Scope for active sessions only
     */
    public function scopeActiveSessions(Builder $query): void
    {
        $query->whereNull('acctstoptime');
    }
}