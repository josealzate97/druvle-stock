@extends('backend.layouts.main')

@section('title', 'Dashboard Soporte')

@push('styles')
    @vite(['resources/css/modules/dashboard.css', 'resources/css/modules/support-dashboard.css'])
@endpush

@push('scripts')
    <script>
        window.supportDashboardData = {
            planDistribution: @json($planDistribution),
            tenantGrowth: @json($tenantGrowth),
        };
    </script>
    @vite(['resources/js/modules/support-dashboard.js'])
@endpush

@section('content')

<div class="container-fluid p-4">

    @push('breadcrumb')
        @include('backend.components.breadcrumb')
    @endpush

    {{-- Stat cards --}}
    <div class="dashboard-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon bg-soft-primary"><i class="fas fa-building"></i></span>
                <span class="stat-meta">Registrados</span>
            </div>
            <div class="stat-label">Total Negocios</div>
            <div class="stat-value">{{ $totalTenants }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon bg-soft-success"><i class="fas fa-check-circle"></i></span>
                <span class="stat-meta">Operativos</span>
            </div>
            <div class="stat-label">Negocios Activos</div>
            <div class="stat-value">{{ $activeTenants }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon bg-soft-purple"><i class="fas fa-users"></i></span>
                <span class="stat-meta">En plataforma</span>
            </div>
            <div class="stat-label">Total Usuarios</div>
            <div class="stat-value">{{ $totalUsers }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-icon bg-soft-warning"><i class="fas fa-hourglass-half"></i></span>
                <span class="stat-pill">Próximos 7 días</span>
            </div>
            <div class="stat-label">Pruebas por vencer</div>
            <div class="stat-value">{{ $expiringTrials }}</div>
        </div>

    </div>

    {{-- Gráficos --}}
    <div class="dashboard-row mb-4">

        <div class="card dashboard-card">
            <div class="dashboard-card-header dashboard-card-header--accent">
                <div class="dashboard-title">
                    <span class="dashboard-pill">Crecimiento</span>
                    <h5>Negocios Registrados</h5>
                    <p>Últimos 6 meses de incorporaciones.</p>
                </div>
            </div>
            <div class="chart-wrapper" id="tenantGrowthWrapper">
                <canvas id="tenantGrowthChart" height="140"></canvas>
                <div class="sd-chart-empty" id="tenantGrowthEmpty" style="display:none;">
                    <span class="sd-empty-icon sd-empty-icon--sm">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    <p class="sd-empty-title">Sin datos de crecimiento</p>
                    <p class="sd-empty-desc">Aún no hay negocios registrados para mostrar el historial de incorporaciones.</p>
                </div>
            </div>
        </div>

        <div class="card dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h5>Distribución por Plan</h5>
                    <p>Proporción Free / Basic / Pro</p>
                </div>
            </div>
            <div class="chart-wrapper" id="planDistributionWrapper">
                <canvas id="planDistributionChart" height="220"></canvas>
                <div class="sd-chart-empty" id="planDistributionEmpty" style="display:none;">
                    <span class="sd-empty-icon sd-empty-icon--sm">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    <p class="sd-empty-title">Sin distribución de planes</p>
                    <p class="sd-empty-desc">No hay negocios registrados para mostrar la proporción por plan.</p>
                </div>
            </div>
            <div class="chart-legend" id="planDistributionLegend"></div>
        </div>

    </div>

    {{-- Tabla de negocios --}}
    <div class="card dashboard-card">
        <div class="dashboard-card-header dashboard-card-header--accent">
            <div class="dashboard-title">
                <span class="dashboard-pill">Directorio</span>
                <h5>Todos los Negocios</h5>
                <p>Listado con métricas clave de cada tenant.</p>
            </div>
            <a href="{{ route('tenants.index') }}" class="btn btn-link dashboard-link">Gestionar</a>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless align-middle section-table mb-0">
                <thead>
                    <tr>
                        <th>Negocio</th>
                        <th>Plan</th>
                        <th class="text-center">Usuarios</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Ventas</th>
                        <th>Prueba hasta</th>
                        <th>Estado</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenantsWithMetrics as $row)
                        @php
                            $planLabels  = [1 => 'Free', 2 => 'Basic', 3 => 'Pro'];
                            $planClasses = [1 => 'table-chip-abbr', 2 => 'table-chip-blue', 3 => 'table-chip-gold'];
                        @endphp

                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="support-dash-avatar">
                                        {{ strtoupper(substr($row->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $row->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">{{ $row->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="table-chip {{ $planClasses[$row->plan] ?? 'table-chip-abbr' }}">
                                    {{ $planLabels[$row->plan] ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">{{ $row->users_count }}</td>
                            <td class="text-center fw-bold">{{ $row->products_count }}</td>
                            <td class="text-center fw-bold">{{ $row->sales_count }}</td>
                            <td class="text-muted" style="font-size:.82rem;">
                                @if($row->trial_ends_at)
                                    @php $trialDate = \Carbon\Carbon::parse($row->trial_ends_at); @endphp
                                    <span class="{{ $trialDate->isPast() ? 'text-danger' : ($trialDate->diffInDays() <= 7 ? 'text-warning fw-bold' : '') }}">
                                        {{ $trialDate->format('d/m/Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($row->status)
                                    <span class="status-pill status-pill-success">Activo</span>
                                @else
                                    <span class="status-pill status-pill-muted">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('tenants.switch', $row->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-purple py-1 px-2"
                                        title="Entrar al negocio" {{ !$row->status ? 'disabled' : '' }}>
                                        <i class="fas fa-sign-in-alt me-1"></i> Entrar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="sd-empty-state">
                                    <span class="sd-empty-icon">
                                        <i class="fas fa-store-slash"></i>
                                    </span>
                                    <p class="sd-empty-title">Sin negocios registrados</p>
                                    <p class="sd-empty-desc">Cuando un negocio se registre en la plataforma aparecerá aquí con sus métricas.</p>
                                    <a href="{{ route('tenants.index') }}" class="btn btn-sm btn-purple px-4">
                                        <i class="fas fa-plus me-1"></i> Crear negocio
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
