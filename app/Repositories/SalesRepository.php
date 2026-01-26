<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;

class SalesRepository {

    /**
     * Obtiene los productos activos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveProducts() {

        // Trae los productos activos con la relación tax
        $products = Product::where('status', Product::ACTIVE)
        ->with(['tax:id,name,rate'])
        ->get();

        return $products;

    }


    /**
     * Obtiene las categorías activas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategories() {

        return Category::where('status', Category::ACTIVE)->get();

    }

    /**
     * Obtiene el historial de ventas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSalesHistory() {

        return Sale::with('client')
        ->orderBy('created_at', 'desc')
        ->get();

    }

}