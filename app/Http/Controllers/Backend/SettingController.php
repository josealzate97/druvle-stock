<?php

namespace App\Http\Controllers\Backend;
use App\Models\Settings as Setting;
use App\Models\Tax as Tax;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller {
    
    /**
     * Función para mostrar la vista de configuración
     * @return \Illuminate\View\View
    */
    public function index() {

        $settings = Setting::first(); // Obtiene la configuración actual
        $taxes = Tax::all();

        return view('backend.settings.index', compact('settings', 'taxes'));

    }

    /**
     * Función para actualizar la configuración
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function update(Request $request) {

        // Validación de los datos del formulario
        $request->validate([
            'id' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'nit' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $settings = Setting::first();

        $settings->update($request->only([
            'id',
            'company_name',
            'nit',
            'phone',
            'address',
        ]));

        // Retorna un objeto de respuesta con un mensaje de éxito sin ruta
        return response()->json([
            'success' => true, 
            'message' => 'Configuración actualizada correctamente'
        ]);

    }

}