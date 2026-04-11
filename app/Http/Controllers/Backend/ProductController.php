<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

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
        $data['has_sizes'] = filter_var($data['has_sizes'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['purchase_price'] = $this->parseNullableDecimal($data['purchase_price'] ?? null);
        $data['sale_price'] = $this->parseNullableDecimal($data['sale_price'] ?? null);
        $data['quantity'] = isset($data['quantity']) && $data['quantity'] !== '' ? (int) $data['quantity'] : null;

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sale_price' => 'nullable|required_if:has_sizes,false|numeric|min:0',
            'purchase_price' => 'nullable|required_if:has_sizes,false|numeric|min:0',
            'quantity' => 'nullable|required_if:has_sizes,false|integer|min:1',
            'has_sizes' => 'required|boolean',
            'taxable' => 'required|boolean',
            'sizes' => 'nullable|required_if:has_sizes,true|array|min:1',
            'sizes.*.name' => 'required_with:sizes|string|max:20',
            'sizes.*.price' => 'required_with:sizes|numeric|min:0',
            'sizes.*.quantity' => 'required_with:sizes|integer|min:0',
            'sizes.*.status' => 'nullable|boolean',
        ])->validate();
        

        $validated['status'] = true; // Asignar estado activo por defecto
        $validated['tax_id'] = isset($data['tax_id']) ? $data['tax_id'] : null; // Asignar tax_id si existe

        $validated['notes'] = isset($data['notes']) ? $data['notes'] : null; // Asignar nota si existe
        $validated['sizes'] = $validated['has_sizes'] ? ($validated['sizes'] ?? []) : [];

        if ($validated['has_sizes'] && empty($validated['sizes'])) {
            return response()->json([
                'success' => false,
                'message' => 'Debes agregar al menos una talla.'
            ], 422);
        }

        $product = DB::transaction(function () use ($validated) {
            $product = Product::create([
                'id' => Str::uuid()->toString(),
                'name' => $validated['name'],
                'code' => $validated['code'],
                'category_id' => $validated['category_id'],
                'sale_price' => $validated['sale_price'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'has_sizes' => $validated['has_sizes'],
                'taxable' => $validated['taxable'],
                'tax_id' => $validated['tax_id'],
                'status' => $validated['status'],
                'notes' => $validated['notes']
            ]);

            $sizeRows = collect($validated['sizes'] ?? [])->map(function ($size) {
                return [
                    'name' => $size['name'],
                    'price' => floatval(str_replace(',', '.', $size['price'])),
                    'quantity' => (int) $size['quantity'],
                    'status' => isset($size['status']) ? (bool) $size['status'] : ProductSize::ACTIVE,
                ];
            })->toArray();

            if (!empty($sizeRows)) {
                $product->sizes()->createMany($sizeRows);
            }

            return $product;
        });

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

        $product = Product::with('sizes')->findOrFail($id);

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
        ->with(['tax:id,name,rate', 'sizes'])
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
        $data['has_sizes'] = filter_var($data['has_sizes'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Convertir los precios a formato numérico
        // Reemplazar comas por puntos para asegurar el formato correcto
        $data['purchase_price'] = $this->parseNullableDecimal($data['purchase_price'] ?? null);
        $data['sale_price'] = $this->parseNullableDecimal($data['sale_price'] ?? null);
        $data['quantity'] = isset($data['quantity']) && $data['quantity'] !== '' ? (int) $data['quantity'] : null;

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sale_price' => 'nullable|required_if:has_sizes,false|numeric|min:0',
            'purchase_price' => 'nullable|required_if:has_sizes,false|numeric|min:0',
            'quantity' => 'nullable|required_if:has_sizes,false|integer|min:1',
            'has_sizes' => 'required|boolean',
            'taxable' => 'required|boolean',
            'sizes' => 'nullable|required_if:has_sizes,true|array|min:1',
            'sizes.*.name' => 'required_with:sizes|string|max:20',
            'sizes.*.price' => 'required_with:sizes|numeric|min:0',
            'sizes.*.quantity' => 'required_with:sizes|integer|min:0',
            'sizes.*.status' => 'nullable|boolean',
        ])->validate();

        $validated['tax_id'] = isset($data['tax_id']) ? $data['tax_id'] : null; // Asignar tax_id si existe
        $validated['status'] = Product::ACTIVE;
        $validated['notes'] = isset($data['notes']) ? $data['notes'] : null; // Asignar nota si existe
        $validated['sale_price'] = $validated['sale_price'] ?? null;
        $validated['purchase_price'] = $validated['purchase_price'] ?? null;
        $validated['quantity'] = $validated['quantity'] ?? null;
        $sizes = $validated['has_sizes'] ? ($validated['sizes'] ?? []) : [];
        unset($validated['sizes']);

        if ($validated['has_sizes'] && empty($sizes)) {
            return response()->json([
                'success' => false,
                'message' => 'Debes agregar al menos una talla.'
            ], 422);
        }

        $product = Product::find($id);

        if (!$product) {

            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);

        }

        DB::transaction(function () use ($product, $validated, $sizes) {
            $product->update($validated);

            $product->sizes()->delete();

            $sizeRows = collect($sizes)->map(function ($size) {
                return [
                    'name' => $size['name'],
                    'price' => floatval(str_replace(',', '.', $size['price'])),
                    'quantity' => (int) $size['quantity'],
                    'status' => isset($size['status']) ? (bool) $size['status'] : ProductSize::ACTIVE,
                ];
            })->toArray();

            if (!empty($sizeRows)) {
                $product->sizes()->createMany($sizeRows);
            }
        });

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

    private function parseNullableDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return floatval(str_replace(',', '.', (string) $value));
    }
}
