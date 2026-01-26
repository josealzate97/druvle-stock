<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductController extends Controller {

    /**
     * Muestra la lista de productos.
     * 
     * @return \Illuminate\View\View
     * Retorna la vista `backend.products.index` con la lista de productos.
    */
    public function index(Request $request) {

        $products = Product::orderBy('name', 'asc')->paginate(10);

        $categories = \App\Models\Category::where('status', \App\Models\Category::ACTIVE)
        ->orderBy('name', 'asc')->get();

        $taxes = \App\Models\Tax::where('status', \App\Models\Tax::ACTIVE)
        ->orderBy('name', 'asc')->get();

        return view('backend.products.index', compact('products', 'categories', 'taxes'));

    }

    /**
     * Crea un nuevo producto.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON con el estado de la creación del producto.
    */
    public function create(Request $request) {

        $data = $request->all();
        
        $data['taxable'] = $request->has('taxable') ? Product::IS_TAXABLE : Product::IS_NOT_TAXABLE;
        $data['purchase_price'] = floatval(str_replace(',', '.', $data['purchase_price']));
        $data['sale_price'] = floatval(str_replace(',', '.', $data['sale_price']));

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'taxable' => 'required|boolean'
        ])->validate();
        

        $validated['status'] = true; // Asignar estado activo por defecto
        $validated['tax_id'] = isset($data['tax_id']) ? $data['tax_id'] : null; // Asignar tax_id si existe

        $validated['notes'] = isset($data['notes']) ? $data['notes'] : null; // Asignar nota si existe

        $product = Product::create([
            'id' => Str::uuid()->toString(),
            'name' => $validated['name'],
            'code' => $validated['code'],
            'category_id' => $validated['category_id'],
            'sale_price' => $validated['sale_price'],
            'purchase_price' => $validated['purchase_price'],
            'quantity' => $validated['quantity'],
            'taxable' => $validated['taxable'],
            'tax_id' => $validated['tax_id'],
            'status' => $validated['status'],
            'notes' => $validated['notes']
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Producto creado correctamente', 
            'product' => $product
        ]);

    }

    /**
     * Edita un producto existente.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON con los datos del producto a editar.
    */
    public function getProduct($id) {

        $product = Product::findOrFail($id);

        return response()->json([
            'success' => true, 
            'product' => $product
        ]);
    }

    /**
     * Obtiene los productos activos.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON con la lista de productos activos.
    */
    public function getActiveProducts() {

        // Trae los productos activos con la relación tax
        $products = Product::where('status', Product::ACTIVE)
        ->with(['tax:id,name,rate'])
        ->get();

        
        return response()->json($products);

    }

    /**
     * Actualiza un producto existente.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON con el estado de la actualización del producto.
    */
    public function update(Request $request, $id) {

        $data = $request->all();

        $data['taxable'] = $request->has('taxable') ? Product::IS_TAXABLE : Product::IS_NOT_TAXABLE;

        // Convertir los precios a formato numérico
        // Reemplazar comas por puntos para asegurar el formato correcto
        $data['purchase_price'] = floatval(str_replace(',', '.', $data['purchase_price']));
        $data['sale_price'] = floatval(str_replace(',', '.', $data['sale_price']));

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'taxable' => 'required|boolean'
        ])->validate();

        $validated['tax_id'] = isset($data['tax_id']) ? $data['tax_id'] : null; // Asignar tax_id si existe
        $validated['status'] = Product::ACTIVE;
        $validated['notes'] = isset($data['notes']) ? $data['notes'] : null; // Asignar nota si existe

        $product = Product::find($id);

        if (!$product) {

            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);

        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente'
        ]);

    }

    /**
     * Desactiva un producto.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito de la desactivación del producto.
    */
    public function delete($id) {
        
        // Encuentra el producto por su ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
            ], 404);
        }

        // Marca el producto como eliminado (soft delete)
        $product->delete_date = now();
        $product->status = Product::INACTIVE;
        $product->save();

        return response()->json([
            'success' => true, 
            'message' => 'Producto desactivado correctamente'
        ]);
    }

    /**
     * Activa un producto.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * Retorna una respuesta JSON indicando el éxito de la activación del producto.
    */
    public function activate($id) {

       // Encuentra el producto por su ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $product->delete_date = null;
        $product->status = Product::ACTIVE;
        $product->save();

        return response()->json([
            'success' => true, 
            'message' => 'Producto activado correctamente'
        ]);
    }
}