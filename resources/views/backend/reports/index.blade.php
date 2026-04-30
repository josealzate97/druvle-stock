@extends('backend.layouts.main')

@section('title', 'Reportes & Estadísticas')

@push('styles')
    @vite(['resources/css/modules/reports.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/reports.js'])
@endpush

@section('content')

    <div class="container-fluid p-4" x-data="reportsApp()" x-init="init()">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'reports.index',
                    'icon' => 'fas fa-chart-bar',
                    'label' => 'Reportes & Estadísticas'
                ]
            ])
        @endpush

        <div class="card p-4 section-hero">

            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">

                <div class="section-hero-icon">
                    <i class="fas fa-chart-line"></i>
                </div>

                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-0">Reportes & Estadísticas</h2>
                    <div class="text-muted small fw-bold">
                        Analiza el rendimiento de tu negocio en cualquier momento y toma mejores decisiones con nuestros reportes.
                    </div>
                </div>

            </div>

        </div>

        <div class="card p-2 mt-4 module-tabs-bar module-tabs-connected">
            <ul class="nav nav-pills module-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{'active': activeTab === 'productos'}" @click="setTab('productos')">
                        <i class="fas fa-boxes-stacked me-1"></i>Productos
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{'active': activeTab === 'ventas'}" @click="setTab('ventas')">
                        <i class="fas fa-receipt me-1"></i>Ventas
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{'active': activeTab === 'impuestos'}" @click="setTab('impuestos')">
                        <i class="fas fa-percent me-1"></i>Impuestos
                    </button>
                </li>
            </ul>
        </div>

        <div class="card p-4 mt-0 section-card module-tabs-content">

            <div class="tab-content p-4" id="reportTabsContent">

                <!-- Productos -->
                <div x-show="activeTab === 'productos'">

                    <!-- Filtro de productos -->
                    <div class="row mb-3 reports-filters">

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.productos.from" placeholder="Desde">
                        </div>
                        
                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.productos.to" placeholder="Hasta">
                        </div>
                        
                        <div class="col-3 d-flex gap-2">
                            <button class="btn reports-export-btn btn-sm w-100" x-show="!loading && data.productos.length > 0" @click="openExportModal('productos')">
                                <i class="fas fa-file-export me-2"></i>Exportar
                            </button>
                        </div>
                        <div class="col-3" x-show="loading || data.productos.length === 0"></div>

                        <div class="col-3">
                            <button class="btn reports-search-btn btn-sm w-100" @click="fetchProductos()">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </div>

                    </div>

                    <!-- Totalizador de productos como stat-cards -->
                    <div class="reports-stat-grid mt-3 mb-1" x-show="!loading && data.productos.length > 0">
                        <div class="stat-card reports-stat-card reports-stat-card--primary">
                            <div class="stat-top">
                                <span class="stat-label">Total Productos</span>
                                <span class="stat-icon bg-soft-primary"><i class="fas fa-cubes"></i></span>
                            </div>
                            <div class="stat-value" x-text="data.productos.length"></div>
                            <div class="stat-meta">productos en stock</div>
                        </div>
                        <div class="stat-card reports-stat-card reports-stat-card--success">
                            <div class="stat-top">
                                <span class="stat-label">Valor Total en Stock</span>
                                <span class="stat-icon bg-soft-success"><i class="fas fa-euro-sign"></i></span>
                            </div>
                            <div class="stat-value" x-text="formatCurrency(data.productos.reduce((sum, prod) => sum + toNumber(prod.stock_value), 0))"></div>
                            <div class="stat-meta">valor de inventario</div>
                        </div>
                    </div>

                    <hr>

                    <template x-if="loading" class="col-12 text-center my-3">
                        <div class="reports-loader">
                            <span class="loader-spinner"></span>
                            <span class="loader-text">Cargando reporte de productos...</span>
                        </div>
                    </template>

                    <!-- Tabla de productos -->
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle section-table" x-show="!loading">

                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Fecha entrada</th>
                                <th>Cantidad</th>
                                <th>Precio compra</th>
                                <th>Precio venta</th>
                            </tr>
                        </thead>

                        <tbody>

                            <template x-for="prod in data.productos" :key="prod.id">
                                <tr>
                                    <td x-text="prod.name"></td>
                                    <td x-text="formatDate(prod.creation_date)"></td>
                                    <td x-text="prod.quantity"></td>
                                    <td x-text="prod.purchase_price !== null ? Number(prod.purchase_price).toFixed(2) + ' €' : '-'"></td>
                                    <td x-text="prod.sale_price !== null ? Number(prod.sale_price).toFixed(2) + ' €' : '-'"></td>
                                </tr>
                            </template>

                            <template x-if="!loading && data.productos.length === 0">
                                <tr>
                                    <td colspan="5">
                                        <div class="sd-empty-state">
                                            <span class="sd-empty-icon sd-empty-icon--sm">
                                                <i class="fas fa-box-open"></i>
                                            </span>
                                            <p class="sd-empty-title">Sin datos de productos</p>
                                            <p class="sd-empty-desc">No se encontraron productos para el rango de fechas seleccionado.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                        </tbody>

                        </table>
                    </div>

                </div>

                <!-- Ventas -->
                <div x-show="activeTab === 'ventas'">

                    <!-- Filtros y tabla de ventas similar -->
                    <div class="row mb-3 reports-filters">

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.ventas.from" placeholder="Desde">
                        </div>
                        
                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.ventas.to" placeholder="Hasta">
                        </div>
                        
                        <div class="col-3 d-flex gap-2">
                            <button class="btn reports-export-btn btn-sm w-100" x-show="!loading && data.ventas.length > 0" @click="openExportModal('ventas')">
                                <i class="fas fa-file-export me-2"></i>Exportar
                            </button>
                        </div>
                        <div class="col-3" x-show="loading || data.ventas.length === 0"></div>

                        <div class="col-3">
                            <button class="btn reports-search-btn btn-sm w-100" @click="fetchVentas()">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </div>

                    </div>

                    <!-- Totalizador de ventas como stat-cards -->
                    <div class="reports-stat-grid mt-3 mb-1" x-show="!loading && data.ventas.length > 0">
                        <div class="stat-card reports-stat-card reports-stat-card--primary">
                            <div class="stat-top">
                                <span class="stat-label">Total Ventas</span>
                                <span class="stat-icon bg-soft-primary"><i class="fas fa-receipt"></i></span>
                            </div>
                            <div class="stat-value" x-text="data.ventas.length"></div>
                            <div class="stat-meta">ventas registradas</div>
                        </div>
                        <div class="stat-card reports-stat-card reports-stat-card--success">
                            <div class="stat-top">
                                <span class="stat-label">Total Facturado</span>
                                <span class="stat-icon bg-soft-success"><i class="fas fa-euro-sign"></i></span>
                            </div>
                            <div class="stat-value" x-text="formatCurrency(data.ventas.reduce((sum, venta) => sum + toNumber(venta.subtotal), 0))"></div>
                            <div class="stat-meta">en ventas netas</div>
                        </div>
                    </div>

                    <hr>

                    <template x-if="loading" class="text-center my-3">
                        <div class="reports-loader">
                            <span class="loader-spinner"></span>
                            <span class="loader-text">Cargando reporte de ventas...</span>
                        </div>
                    </template>

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle section-table" x-show="!loading">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Fecha de venta</th>
                                <th>Subtotal</th>
                                <th>Tax</th>
                                <th>Total</th>
                                <th>Tipo de pago</th>
                            </tr>
                        </thead>

                        <tbody>
                            
                            <template x-for="venta in data.ventas" :key="venta.id">
                                <tr>
                                    <td><span class="badge sale-code-badge" x-text="venta.code"></span></td>
                                    <td x-text="venta.client.name == '' ? 'Anonimo' : venta.client.name"></td>
                                    <td x-text="formatDate(venta.sale_date)"></td>
                                    <td x-text="venta.subtotal + ' €'"></td>
                                    <td x-text="venta.tax + ' €'"></td>
                                    <td x-text="venta.total + ' €'"></td>
                                    <td>
                                        <span class="report-payment-badge" x-text="venta.type_payment == 1 ? 'EFECTIVO' : (venta.type_payment == 2 ? 'BIZUM' : 'TPV')"></span>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && data.ventas.length === 0">
                                <tr>
                                    <td colspan="7">
                                        <div class="sd-empty-state">
                                            <span class="sd-empty-icon sd-empty-icon--sm">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </span>
                                            <p class="sd-empty-title">Sin ventas en el periodo</p>
                                            <p class="sd-empty-desc">No se encontraron ventas para el rango de fechas seleccionado.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                        </table>
                    </div>

                </div>

                <!-- Impuestos -->
                <div x-show="activeTab === 'impuestos'">
                    
                    <!-- Filtros y tabla de impuestos similar -->
                    <div class="row mb-3 reports-filters">

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.impuestos.from" placeholder="Desde">
                        </div>

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.impuestos.to" placeholder="Hasta">
                        </div>
                        
                        <div class="col-3 d-flex gap-2">
                            <button class="btn reports-export-btn btn-sm w-100" x-show="!loading && data.impuestos.length > 0" @click="openExportModal('impuestos')">
                                <i class="fas fa-file-export me-2"></i>Exportar
                            </button>
                        </div>
                        <div class="col-3" x-show="loading || data.impuestos.length === 0"></div>

                        <div class="col-3">
                            <button class="btn reports-search-btn btn-sm w-100" @click="fetchImpuestos()">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </div>

                    </div>

                    <!-- Totalizador de impuestos como stat-card -->
                    <div class="reports-stat-grid mt-3 mb-1" x-show="!loading && data.impuestos.length > 0">
                        <div class="stat-card reports-stat-card reports-stat-card--warning">
                            <div class="stat-top">
                                <span class="stat-label">Total Impuestos Recaudados</span>
                                <span class="stat-icon bg-soft-warning"><i class="fas fa-percent"></i></span>
                            </div>
                            <div class="stat-value" x-text="formatCurrency(data.impuestos.reduce((sum, imp) => sum + toNumber(imp.total_tax), 0))"></div>
                            <div class="stat-meta">total de IVA recaudado</div>
                        </div>
                    </div>

                    <hr>

                    <template x-if="loading" class="text-center my-3">
                        <div class="reports-loader">
                            <span class="loader-spinner"></span>
                            <span class="loader-text">Cargando reporte de impuestos...</span>
                        </div>
                    </template>

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle section-table" x-show="!loading">

                        <thead>
                            <tr>
                                <th>Impuesto</th>
                                <th>Total recaudado</th>
                            </tr>
                        </thead>

                        <tbody>

                            <template x-for="imp in data.impuestos" :key="imp.id ?? imp.name">
                                <tr>
                                    <td x-text="imp.name"></td>
                                    <td x-text="formatCurrency(toNumber(imp.total_tax))"></td>
                                </tr>
                            </template>

                            <template x-if="!loading && data.impuestos.length === 0">
                                <tr>
                                    <td colspan="2">
                                        <div class="sd-empty-state">
                                            <span class="sd-empty-icon sd-empty-icon--sm">
                                                <i class="fas fa-percent"></i>
                                            </span>
                                            <p class="sd-empty-title">Sin datos de impuestos</p>
                                            <p class="sd-empty-desc">No se encontraron impuestos recaudados para el rango de fechas seleccionado.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

        <div class="modal fade" id="reportsExportModal" tabindex="-1" aria-labelledby="reportsExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered reports-export-modal-dialog">
                <div class="modal-content form-modal">
                    <div class="modal-header reports-export-modal-header">
                        <div class="modal-title-block">
                            <h5 class="modal-title" id="reportsExportModalLabel">
                                <span class="modal-icon">
                                    <i class="fas fa-file-export"></i>
                                </span>
                                Exportar Reporte
                            </h5>
                            <span class="modal-subtitle">Elige el formato de exportación.</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="button" class="btn btn-outline-success w-100" @click="exportCurrent('excel')">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </button>
                            <button type="button" class="btn btn-outline-danger w-100" @click="exportCurrent('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
