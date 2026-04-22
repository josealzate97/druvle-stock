<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        $tenantId = self::resolveTenantId();

        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }

    public static function resolveTenantId(): ?string
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        if ($user->rol === User::ROLE_SUPPORT) {
            return session('active_tenant_id');
        }

        return $user->tenant_id;
    }
}
