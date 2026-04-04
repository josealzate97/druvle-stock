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

            $salesTrendByPeriod = [
                'weekly' => [
                    'labels' => [],
                    'values' => [],
                    'description' => 'Visualización semanal de ventas de las últimas 8 semanas.',
                ],
                'monthly' => [
                    'labels' => [],
                    'values' => [],
                    'description' => 'Visualización de ventas de los últimos 7 meses con detalle mensual.',
                ],
                'quarterly' => [
                    'labels' => [],
                    'values' => [],
                    'description' => 'Visualización de ventas de los últimos 4 trimestres.',
                ],
                'semiannual' => [
                    'labels' => [],
                    'values' => [],
                    'description' => 'Visualización de ventas de los últimos 4 semestres.',
                ],
            ];

            for ($i = 7; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();

                $salesTrendByPeriod['weekly']['labels'][] = 'Sem ' . $weekStart->format('W');
                $salesTrendByPeriod['weekly']['values'][] = Sale::whereBetween('created_at', [$weekStart, $weekEnd])->sum('total');
            }

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->copy()->startOfMonth();
                $monthEnd = $date->copy()->endOfMonth();

                $salesTrendByPeriod['monthly']['labels'][] = $date->translatedFormat('M');
                $salesTrendByPeriod['monthly']['values'][] = Sale::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total');
            }

            for ($i = 3; $i >= 0; $i--) {
                $quarterStart = Carbon::now()->subQuarters($i)->startOfQuarter();
                $quarterEnd = $quarterStart->copy()->endOfQuarter();

                $salesTrendByPeriod['quarterly']['labels'][] = 'T' . $quarterStart->quarter . ' ' . $quarterStart->year;
                $salesTrendByPeriod['quarterly']['values'][] = Sale::whereBetween('created_at', [$quarterStart, $quarterEnd])->sum('total');
            }

            for ($i = 3; $i >= 0; $i--) {
                $baseDate = Carbon::now()->subMonths($i * 6);
                $month = (int) $baseDate->format('n');
                $semesterNumber = $month <= 6 ? 1 : 2;
                $semesterStartMonth = $semesterNumber === 1 ? 1 : 7;

                $semesterStart = Carbon::create($baseDate->year, $semesterStartMonth, 1)->startOfDay();
                $semesterEnd = $semesterStart->copy()->addMonths(6)->subDay()->endOfDay();

                $salesTrendByPeriod['semiannual']['labels'][] = 'S' . $semesterNumber . ' ' . $baseDate->year;
                $salesTrendByPeriod['semiannual']['values'][] = Sale::whereBetween('created_at', [$semesterStart, $semesterEnd])->sum('total');
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
                'salesTrendByPeriod' => $salesTrendByPeriod,
                'defaultSalesPeriod' => 'monthly',
                'topCategories' => $topCategories,
            ]);
        }

}
