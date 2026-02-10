<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller {

    /**
     * Muestra la lista de categorías.
     * 
     * @return \Illuminate\View\View
     * Retorna la vista `backend.categories.index` con la lista de categorías activas.
     */
    public function index() {

        // Obtiene todas las categorías activas
        $categories = Category::withCount('products')
            ->orderBy('name', 'asc')
            ->paginate(10);

        // Retorna la vista con las categorías
        return view('backend.categories.index', compact('categories'));

    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     * 
     * @return \Illuminate\View\View
     * Retorna la vista `backend.categories.create` para crear una nueva categoría.
    */
    public function create(Request $request) {

        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        // Crear la nueva categoría
        $category = Category::create([
            'id' => Str::uuid()->toString(), // Genera un UUID único
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
            'icon' => $validated['icon'],
            'color' => $validated['color'],
            'status' => Category::ACTIVE, // Estado activo por defecto
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada correctamente',
            'category' => $category
        ]);

    }

    /**
     * Muestra el formulario para editar una categoría existente.
     * 
     * @param string $id
     * ID de la categoría a editar.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Retorna los datos de la categoría para ser editados.
    */
    public function getCategory($id) {
        
        // Busca la categoría por ID
        $category = Category::findOrFail($id);

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Actualiza una categoría existente.
     * 
     * @param \Illuminate\Http\Request $request
     * Objeto de la solicitud HTTP que contiene los datos enviados por el cliente.
     * 
     * @param string $id
     * ID de la categoría a actualizar.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito o error de la operación.
    */
    public function update(Request $request, $id) {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada correctamente',
        ]);

    }

    /**
     * Elimina una categoría estableciendo su estado a inactivo.
     * 
     * @param string $id
     * ID de la categoría a eliminar.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito o error de la operación.
    */
    public function delete($id) {

        $category = Category::findOrFail($id);

        // Cambia el estado de la categoría a inactiva
        $category->update(['status' => Category::INACTIVE]);
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente'
        ]);
        
    }

    /**
     * Activa una categoría estableciendo su estado a activo.
     * 
     * @param string $id
     * ID de la categoría a activar.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito o error de la operación.
     */
    public function activate($id) {
        $category = Category::findOrFail($id);

        $category->update(['status' => Category::ACTIVE]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría activada correctamente'
        ]);
    }


}
