<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DefaultController {

        public function dashboard() {

            // Consultar categorías activas
            $activeCategories = Category::where('status', true)->count();

            // Consultar productos
            $totalProducts = Product::where('status', true)->count();

            // Consultar ventas del mes
            $monthlySales = Sale::whereYear('created_at',now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

            // Retornar vista con los datos
            return view('backend.home', [
                'activeCategories' => $activeCategories,
                'totalProducts' => $totalProducts,
                'monthlySales' => $monthlySales,
            ]);
        }

}
