<?php

namespace App\Modules\Customer\Models;

use App\Core\Abstract\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CustomerDocument extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'document_type', // 'id_copy', 'contract', 'installation_form'
        'file_path',
        'file_name',
        'mime_type'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the absolute URL for the document (protected by middleware usually)
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('private')->url($this->file_path);
    }
}