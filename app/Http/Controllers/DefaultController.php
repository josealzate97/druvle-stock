<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DefaultController {

        /**
         * Devuelve el tenant_id activo para el contexto actual:
         * - ROLE_SUPPORT con tenant en sesión → usa la sesión
         * - Cualquier otro usuario → usa su propio tenant_id
         */
        private function currentTenantId(): ?string
        {
            if (Auth::user()->rol === User::ROLE_SUPPORT) {
                return session('active_tenant_id');
            }
            return Auth::user()->tenant_id;
        }

        public function dashboard() {

            // Dashboard exclusivo para soporte (solo si NO tiene un tenant activo en sesión)
            if (Auth::user()->rol === User::ROLE_SUPPORT && !session('active_tenant_id')) {
                return $this->supportDashboard();
            }

            $tenantId = $this->currentTenantId();

            // Consultar categorías activas
            $activeCategories = Category::where('status', true)
                ->where('tenant_id', $tenantId)->count();

            // Consultar productos
            $totalProducts = Product::where('status', true)
                ->where('tenant_id', $tenantId)->count();

            // Consultar ventas del mes
            $monthlySales = Sale::where('tenant_id', $tenantId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total');

            $totalSales = Sale::where('tenant_id', $tenantId)->sum('total');

            $lowStockThreshold = 5;
            $lowStockCount = Product::where('status', true)
                ->where('tenant_id', $tenantId)
                ->where('quantity', '<=', $lowStockThreshold)
                ->count();

            $recentSales = Sale::with('client')
                ->where('tenant_id', $tenantId)
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
                $salesTrendByPeriod['weekly']['values'][] = Sale::where('tenant_id', $tenantId)->whereBetween('created_at', [$weekStart, $weekEnd])->sum('total');
            }

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->copy()->startOfMonth();
                $monthEnd = $date->copy()->endOfMonth();

                $salesTrendByPeriod['monthly']['labels'][] = $date->translatedFormat('M');
                $salesTrendByPeriod['monthly']['values'][] = Sale::where('tenant_id', $tenantId)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total');
            }

            for ($i = 3; $i >= 0; $i--) {
                $quarterStart = Carbon::now()->subQuarters($i)->startOfQuarter();
                $quarterEnd = $quarterStart->copy()->endOfQuarter();

                $salesTrendByPeriod['quarterly']['labels'][] = 'T' . $quarterStart->quarter . ' ' . $quarterStart->year;
                $salesTrendByPeriod['quarterly']['values'][] = Sale::where('tenant_id', $tenantId)->whereBetween('created_at', [$quarterStart, $quarterEnd])->sum('total');
            }

            for ($i = 3; $i >= 0; $i--) {
                $baseDate = Carbon::now()->subMonths($i * 6);
                $month = (int) $baseDate->format('n');
                $semesterNumber = $month <= 6 ? 1 : 2;
                $semesterStartMonth = $semesterNumber === 1 ? 1 : 7;

                $semesterStart = Carbon::create($baseDate->year, $semesterStartMonth, 1)->startOfDay();
                $semesterEnd = $semesterStart->copy()->addMonths(6)->subDay()->endOfDay();

                $salesTrendByPeriod['semiannual']['labels'][] = 'S' . $semesterNumber . ' ' . $baseDate->year;
                $salesTrendByPeriod['semiannual']['values'][] = Sale::where('tenant_id', $tenantId)->whereBetween('created_at', [$semesterStart, $semesterEnd])->sum('total');
            }

            $topCategories = DB::table('sale_details')
                ->join('products', 'sale_details.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('sale_details.tenant_id', $tenantId)
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

        private function supportDashboard()
        {
            $totalTenants   = Tenant::count();
            $activeTenants  = Tenant::where('status', true)->count();
            $totalUsers     = User::where('rol', '!=', User::ROLE_SUPPORT)->count();
            $expiringTrials = Tenant::where('status', true)
                ->whereNotNull('trial_ends_at')
                ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
                ->count();

            // Métricas por tenant
            $tenantsWithMetrics = DB::table('tenants')
                ->leftJoin('users',    'users.tenant_id',    '=', 'tenants.id')
                ->leftJoin('products', 'products.tenant_id', '=', 'tenants.id')
                ->leftJoin('sales',    'sales.tenant_id',    '=', 'tenants.id')
                ->select(
                    'tenants.id',
                    'tenants.name',
                    'tenants.slug',
                    'tenants.plan',
                    'tenants.trial_ends_at',
                    'tenants.status',
                    DB::raw('COUNT(DISTINCT users.id) as users_count'),
                    DB::raw('COUNT(DISTINCT products.id) as products_count'),
                    DB::raw('COUNT(DISTINCT sales.id) as sales_count')
                )
                ->groupBy('tenants.id', 'tenants.name', 'tenants.slug', 'tenants.plan', 'tenants.trial_ends_at', 'tenants.status')
                ->orderBy('tenants.name')
                ->get();

            // Distribución por plan
            $planCounts = Tenant::selectRaw('plan, COUNT(*) as total')
                ->groupBy('plan')
                ->pluck('total', 'plan');

            $planLabels = [1 => 'Free', 2 => 'Basic', 3 => 'Pro'];
            $planDistribution = [
                'labels' => array_values($planLabels),
                'values' => [
                    (int)($planCounts[1] ?? 0),
                    (int)($planCounts[2] ?? 0),
                    (int)($planCounts[3] ?? 0),
                ],
            ];

            // Crecimiento de tenants últimos 6 meses
            $tenantGrowth = ['labels' => [], 'values' => []];
            for ($i = 5; $i >= 0; $i--) {
                $date  = Carbon::now()->subMonths($i);
                $start = $date->copy()->startOfMonth();
                $end   = $date->copy()->endOfMonth();

                $tenantGrowth['labels'][] = $date->translatedFormat('M Y');
                $tenantGrowth['values'][] = Tenant::whereBetween('created_at', [$start, $end])->count();
            }

            return view('backend.support-dashboard', compact(
                'totalTenants',
                'activeTenants',
                'totalUsers',
                'expiringTrials',
                'tenantsWithMetrics',
                'planDistribution',
                'tenantGrowth'
            ));
        }

}
