<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    /**
     * Muestra el listado de negocios (tenants).
     */
    public function index()
    {
        $tenants = Tenant::orderBy('name', 'asc')->paginate(10);

        return view('backend.tenants.index', compact('tenants'));
    }

    /**
     * Crea un nuevo tenant.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'plan'          => 'required|integer|in:1,2,3',
            'trial_ends_at' => 'nullable|date',
        ]);

        // Calcular consecutivo y generar slug automático
        $consecutivo = (Tenant::max('consecutivo') ?? 0) + 1;
        $nameNorm    = preg_replace('/[^a-z0-9]+/', '_', mb_strtolower(trim($validated['name'])));
        $nameNorm    = trim($nameNorm, '_');
        $slug        = 'negocio_' . $nameNorm . '_' . str_pad($consecutivo, 3, '0', STR_PAD_LEFT);

        $tenant = Tenant::create([
            'id'            => (string) Str::uuid(),
            'name'          => $validated['name'],
            'slug'          => $slug,
            'consecutivo'   => $consecutivo,
            'plan'          => $validated['plan'],
            'trial_ends_at' => $validated['trial_ends_at'] ?? null,
            'status'        => true,
        ]);

        // Crear automáticamente un usuario admin con credenciales fijas
        $adminEmail = 'admin@' . $slug . '.local';
        $adminPhone = 'T-' . strtoupper(Str::random(10));

        User::create([
            'id'        => (string) Str::uuid(),
            'name'      => $validated['name'],
            'lastname'  => 'Admin',
            'username'  => 'admin',
            'email'     => $adminEmail,
            'phone'     => $adminPhone,
            'password'  => Hash::make('admin@123'),
            'rol'       => User::ROLE_ADMIN,
            'status'    => User::ACTIVE,
            'tenant_id' => $tenant->id,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Negocio creado correctamente',
            'tenant'   => $tenant,
            'admin'    => [
                'username' => 'admin',
                'password' => 'admin@123',
                'slug'     => $slug,
            ],
        ]);
    }

    /**
     * Devuelve los datos de un tenant para edición (JSON).
     */
    public function getTenant($id)
    {
        $tenant = Tenant::findOrFail($id);

        return response()->json([
            'success' => true,
            'tenant'  => $tenant,
        ]);
    }

    /**
     * Actualiza un tenant existente (el slug no se puede cambiar).
     */
    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'plan'          => 'required|integer|in:1,2,3',
            'trial_ends_at' => 'nullable|date',
        ]);

        $tenant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Negocio actualizado correctamente',
        ]);
    }

    /**
     * Desactiva un tenant (soft status).
     */
    public function delete($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Negocio desactivado correctamente',
        ]);
    }

    /**
     * Activa un tenant.
     */
    public function activate($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Negocio activado correctamente',
        ]);
    }
}
