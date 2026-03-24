<?php

namespace App\Core\Abstract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Core\Context\TenantContext;

abstract class BaseModel extends Model
{
    /**
     * The attributes that aren't mass assignable.
     * Use guarded to allow flexibility in a modular system.
     */
    protected $guarded = ['id'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        /** @var TenantContext $context */
        $context = app(TenantContext::class);

        // 1. Global Scope: Automatically filters every SELECT query by tenant_id
        static::addGlobalScope('tenant_filter', function (Builder $builder) use ($context) {
            if ($context->hasTenant()) {
                $builder->where('tenant_id', $context->getTenantId());
            }
        });

        // 2. Model Observer: Automatically assigns tenant_id on every INSERT
        static::creating(function (Model $model) use ($context) {
            if ($context->hasTenant()) {
                // Only set if not already manually assigned (useful for migrations/seeding)
                $model->tenant_id = $model->tenant_id ?? $context->getTenantId();
            }
        });
    }

    /**
     * Helper to bypass tenant scoping for administrative/system tasks.
     */
    public static function systemQuery(): Builder
    {
        return static::withoutGlobalScope('tenant_filter');
    }
}