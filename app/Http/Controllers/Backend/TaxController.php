<?php

namespace App\Http\Controllers\Backend;
use App\Models\Tax;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    
    /**
     * Crea un nuevo impuesto.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito de la operación.
     */
    public function create(Request $request)
    {
        
        // validacion por si rate llega igual a 0
        if ($request->input('rate') <= 0) {

            return response()->json([
                'success' => false,
                'message' => 'La tasa debe ser mayor a 0',
            ], 422);

        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0.01',
            'status' => 'required|boolean',
        ]);

        $validated['status'] = Tax::ACTIVE; // Asignar estado activo por defecto    

        $tax = Tax::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'IVA creado correctamente',
            'tax' => $tax,
        ]);
    }

    /**
     * Muestra la lista de impuestos.
     * 
     * @return \Illuminate\View\View
     * Retorna la vista `backend.taxes.index` con la lista de impuestos.
    */
    public function update(Request $request, $id) {
        
        // validacion por si rate llega igual a 0
        if ($request->input('rate') <= 0) {

            return response()->json([
                'success' => false,
                'message' => 'La tasa debe ser mayor a 0',
            ], 422);
            
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0.01',
            'status' => 'required|boolean',
        ]);

        $tax = Tax::findOrFail($id);
        $tax->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'IVA actualizado correctamente',
        ]);

    }

    /**
     * Elimina un impuesto.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito de la operación.
    */
    public function delete($id) {

        $tax = Tax::findOrFail($id);
        $tax->status = Tax::INACTIVE; // INACTIVE
        $tax->save();

        return response()->json([
            'success' => true,
            'message' => 'IVA eliminado correctamente',
        ]);

    }

    /**
     * Activa o desactiva un impuesto.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito de la operación.
    */
    public function activate($id) {

        $tax = Tax::findOrFail($id);
        $tax->status = !$tax->status; // Cambia el estado
        
        $tax->save();

        return response()->json([
            'success' => true,
            'message' => 'IVA ' . ($tax->status ? 'activado' : 'desactivado') . ' correctamente',
        ]);

    }

}