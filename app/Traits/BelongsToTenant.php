<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the belongs to tenant trait for a model.
     *
     * @return void
     */
    protected static function bootBelongsToTenant()
    {
        // Add global scope to filter by tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('tenant_id')) {
                $builder->where('tenant_id', session()->get('tenant_id'));
            }
        });

        // Automatically set tenant_id on creating
        static::creating(function ($model) {
            if (session()->has('tenant_id')) {
                $model->tenant_id = session()->get('tenant_id');
            }
        });
    }

    /**
     * Define the relationship to the Tenant model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
