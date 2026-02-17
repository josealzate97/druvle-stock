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

        <div class="card p-4 mt-4 section-card">

            <!-- ...tabs... -->
            <ul class="nav nav-pills mb-4" id="reportTabs" role="tablist">
                
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{'active': activeTab === 'productos'}" @click="setTab('productos')">Productos</button>
                </li>
            
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{'active': activeTab === 'ventas'}" @click="setTab('ventas')">Ventas</button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{'active': activeTab === 'impuestos'}" @click="setTab('impuestos')">Impuestos</button>
                </li>

            </ul>

            <div class="tab-content p-4" id="reportTabsContent">

                <!-- Productos -->
                <div x-show="activeTab === 'productos'">

                    <!-- Filtro de productos -->
                    <div class="row mb-3">

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.productos.from" placeholder="Desde">
                        </div>
                        
                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.productos.to" placeholder="Hasta">
                        </div>
                        
                        <div class="col-3 d-flex gap-2">
                            <button class="btn btn-outline-success w-100" @click="exportProductos('excel')">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </button>
                            <button class="btn btn-outline-danger w-100" @click="exportProductos('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </button>
                        </div>

                        <div class="col-3">
                            <button class="btn btn-primary w-100" @click="fetchProductos()">Buscar</button>
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
                                    <td x-text="prod.purchase_price + ' €'"></td>
                                    <td x-text="prod.sale_price + ' €'"></td>
                                </tr>
                            </template>

                            <!-- Totalizador de productos como badges y responsivo -->
                            <div class="row my-5" x-show="!loading && data.productos.length > 0">
                                <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-cubes me-1"></i>
                                        Total productos: <span x-text="data.productos.length"></span>
                                    </span>
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-euro-sign me-1"></i>
                                        Valor total en stock: 
                                        <span x-text="formatCurrency(data.productos.reduce((sum, prod) => sum + (prod.quantity * parseFloat(prod.sale_price || 0)), 0))"></span>
                                    </span>
                                </div>
                            </div>

                            <template x-if="!loading && data.productos.length === 0">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4 fw-bold">
                                        <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                        No hay productos disponibles
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
                    <div class="row mb-3">

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.ventas.from" placeholder="Desde">
                        </div>
                        
                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.ventas.to" placeholder="Hasta">
                        </div>
                        
                        <div class="col-3 d-flex gap-2">

                            <button class="btn btn-outline-success w-100" @click="exportVentas('excel')">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </button>

                            <button class="btn btn-outline-danger w-100" @click="exportVentas('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </button>

                        </div>

                        <div class="col-3">
                            <button class="btn btn-primary w-100" @click="fetchVentas()">Buscar</button>
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
                                    <td x-text="venta.type_payment == 1 ? 'EFECTIVO' : (venta.type_payment == 2 ? 'BIZUM' : 'TVP')"></td>
                                </tr>
                            </template>

                            <!-- Totalizador de ventas -->
                            <div class="row my-5" x-show="!loading && data.ventas.length > 0">
                                <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-receipt me-1"></i>
                                        Total ventas: <span x-text="data.ventas.length"></span>
                                    </span>
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-euro-sign me-1"></i>
                                        Total facturado: 
                                        <span x-text="formatCurrency(data.ventas.reduce((sum, venta) => sum + parseFloat(venta.subtotal || 0), 0))"></span>
                                    </span>
                                </div>
                            </div>

                            <template x-if="!loading && data.ventas.length === 0">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4 fw-bold">
                                        <i class="fas fa-receipt fa-2x mb-2"></i><br>
                                        No hay ventas registradas
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
                    <div class="row mb-3">

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.impuestos.from" placeholder="Desde">
                        </div>

                        <div class="col-3">
                            <input class="form-control" type="date" x-model="filters.impuestos.to" placeholder="Hasta">
                        </div>
                        
                        <div class="col-3 d-flex gap-2">

                            <button class="btn btn-outline-success w-100" @click="exportImpuestos('excel')">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </button>

                            <button class="btn btn-outline-danger w-100" @click="exportImpuestos('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </button>

                        </div>

                        <div class="col-3">
                            <button class="btn btn-primary w-100" @click="fetchImpuestos()">Buscar</button>
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
                                    <td x-text="imp.total_tax"></td>
                                </tr>
                            </template>

                            <!-- Totalizador de impuestos -->
                            <div class="row my-5" x-show="!loading && data.impuestos.length > 0">
                                <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="fas fa-percent me-1"></i>
                                        Total impuestos recaudados: 
                                        <span x-text="formatCurrency(data.impuestos.reduce((sum, imp) => sum + parseFloat(imp.total_tax), 0))"></span>
                                    </span>
                                </div>
                            </div>

                            <template x-if="!loading && data.impuestos.length === 0">
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4 fw-bold">
                                        <i class="fas fa-percent fa-2x mb-2"></i><br>
                                        No hay datos de impuestos
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
