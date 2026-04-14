<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class SwitchTenant
{
    /**
     * Si el usuario es soporte y tiene un tenant activo en sesión,
     * inyecta el tenant en el contenedor para que los controladores
     * puedan acceder a él via app('current_tenant').
     */
    public function handle(Request $request, Closure $next)
    {
        $tenantId = session('active_tenant_id');

        if ($tenantId) {
            // Soporte con tenant activo en sesión
            $tenant = Tenant::find($tenantId);

            if ($tenant) {
                app()->instance('current_tenant', $tenant);
                view()->share('currentTenant', $tenant);
            } else {
                session()->forget('active_tenant_id');
            }
        } elseif (auth()->check() && auth()->user()->tenant_id) {
            // Usuario regular con tenant propio
            $tenant = Tenant::find(auth()->user()->tenant_id);

            if ($tenant) {
                view()->share('userTenant', $tenant);
            }
        }

        return $next($request);
    }
}
