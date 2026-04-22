<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Filtrar automáticamente por tenant en todas las queries
        static::addGlobalScope(new TenantScope());

        // Asignar tenant_id automáticamente al crear
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = TenantScope::resolveTenantId();
            }
        });
    }
}
