<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

            $totalSales = Sale::sum('total');

            $lowStockThreshold = 5;
            $lowStockCount = Product::where('status', true)
                ->where('quantity', '<=', $lowStockThreshold)
                ->count();

            $recentSales = Sale::with('client')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $salesTrend = [];
            $labels = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->translatedFormat('M');
                $salesTrend[] = Sale::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total');
            }

            $topCategories = DB::table('sale_details')
                ->join('products', 'sale_details.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('SUM(sale_details.quantity) as total_qty'))
                ->groupBy('categories.name')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            // Retornar vista con los datos
            return view('backend.home', [
                'activeCategories' => $activeCategories,
                'totalProducts' => $totalProducts,
                'monthlySales' => $monthlySales,
                'totalSales' => $totalSales,
                'lowStockCount' => $lowStockCount,
                'recentSales' => $recentSales,
                'salesTrend' => $salesTrend,
                'salesTrendLabels' => $labels,
                'topCategories' => $topCategories,
            ]);
        }

}
