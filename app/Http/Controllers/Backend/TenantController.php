<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Tenant;

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
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:100|unique:tenants,slug|regex:/^[a-z0-9\-]+$/',
            'plan' => 'required|integer|in:1,2,3',
            'trial_ends_at' => 'nullable|date',
        ]);

        $tenant = Tenant::create([
            'id'            => (string) Str::uuid(),
            'name'          => $validated['name'],
            'slug'          => $validated['slug'],
            'plan'          => $validated['plan'],
            'trial_ends_at' => $validated['trial_ends_at'] ?? null,
            'status'        => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Negocio creado correctamente',
            'tenant'  => $tenant,
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
     * Actualiza un tenant existente.
     */
    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'slug'          => 'required|string|max:100|unique:tenants,slug,' . $id . '|regex:/^[a-z0-9\-]+$/',
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
