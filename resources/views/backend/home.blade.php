@extends('backend.layouts.main')

@section('title', 'Inicio')

@push('styles')
    @vite(['resources/css/modules/dashboard.css'])
@endpush

@push('scripts')

    <script>
        window.dashboardData = {
            salesTrendLabels: @json($salesTrendLabels ?? []),
            salesTrend: @json($salesTrend ?? []),
            topCategories: @json($topCategories ?? []),
        };
    </script>

    @vite(['resources/js/modules/dashboard.js'])
    
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb')
        @endpush

        <div class="dashboard-grid">

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-icon bg-soft-primary"><i class="fas fa-wallet"></i></span>
                    <span class="stat-badge text-success">+12.5%</span>
                </div>
                <div class="stat-label">Ventas Totales</div>
                <div class="stat-value">€ {{ number_format($totalSales ?? 0, 2) }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-icon bg-soft-purple"><i class="fas fa-box"></i></span>
                    <span class="stat-meta">Total Stock</span>
                </div>
                <div class="stat-label">Productos Activos</div>
                <div class="stat-value">{{ number_format($totalProducts ?? 0) }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-icon bg-soft-warning"><i class="fas fa-bell"></i></span>
                    <span class="stat-pill">Acción requerida</span>
                </div>
                <div class="stat-label">Alertas Stock Bajo</div>
                <div class="stat-value">{{ number_format($lowStockCount ?? 0) }} items</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-icon bg-soft-success"><i class="fas fa-chart-line"></i></span>
                    <span class="stat-badge text-success">+8.2%</span>
                </div>
                <div class="stat-label">Ingresos Mensuales</div>
                <div class="stat-value">€ {{ number_format($monthlySales ?? 0, 2) }}</div>
            </div>

        </div>

        <div class="dashboard-row">

            <div class="card dashboard-card">
                <div class="dashboard-card-header dashboard-card-header--accent">
                    <div class="dashboard-title">
                        <span class="dashboard-pill">Performance</span>
                        <h5>Tendencia de Ventas</h5>
                        <p>Visualización de ventas de los últimos 7 meses con detalle mensual.</p>
                    </div>
                    <label class="visually-hidden" for="dashboardPeriod">Periodo</label>
                    <select class="form-select form-select-sm dashboard-select" id="dashboardPeriod" name="dashboardPeriod">
                        <option>Este Año</option>
                    </select>
                </div>
                <div class="chart-wrapper">
                    <canvas id="salesTrendChart" height="140"></canvas>
                </div>
            </div>

            <div class="card dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h5>Categorías Más Vendidas</h5>
                        <p>Distribución por volumen</p>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="topCategoriesChart" height="220"></canvas>
                </div>
                <div class="chart-legend" id="topCategoriesLegend"></div>
            </div>

        </div>

        <div class="card dashboard-card">
            <div class="dashboard-card-header dashboard-card-header--accent">
                <div class="dashboard-title">
                    <span class="dashboard-pill">Últimas ventas</span>
                    <h5>Ventas Recientes</h5>
                    <p>Últimas transacciones registradas y estado actual.</p>
                </div>
                <a href="{{ route('sales.index', ['tab' => 'historial']) }}" class="btn btn-link dashboard-link">Ver todo</a>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless align-middle section-table mb-0">
                    <thead>
                        <tr>
                            <th>Venta</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSales as $sale)
                            <tr>
                                <td><span class="badge sale-code-badge">{{ $sale->code }}</span></td>
                                <td>{{ $sale->client->name ?? 'Anónimo' }}</td>
                                <td>{{ $sale->created_at?->format('d M, Y') }}</td>
                                <td>€ {{ number_format($sale->total, 2) }}</td>
                                <td>
                                    @if ($sale->status == \App\Models\Sale::ACTIVE)
                                        <span class="status-pill status-pill-success">Completado</span>
                                    @else
                                        <span class="status-pill status-pill-muted">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted fw-bold fs-6">No hay ventas recientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection
