@extends('backend.layouts.main')

@section('title', 'Inicio')

@push('scripts')
    @vite(['resources/js/modules/auth.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb')
        @endpush

        <div class="card p-4">

            <!-- Header -->
            <div class="col-12">
                <h3 class="fw-bold">
                    <i class="fas fa-dashboard me-2 color-primary"></i>
                    Dashboard Druvle
                </h3>
                <div class="text-muted fw-bold small">Bienvenido al panel de control</div>
            </div>

            <hr>

            <!-- Contenido Dinamico -->
            <div class="col-12">

                <div class="row g-4 mt-1">

                    <!-- Card 1 -->
                    <div class="col-lg-4 col-md-6 col-sm-12">

                        <div class="rounded-4 bg-white shadow p-4">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-5">Total de Productos</span>
                                <i class="fa fa-box fs-5 color-primary"></i>
                            </div>

                            <div class="fw-bold fs-4">{{ $totalProducts }}</div>
                            <div class="text-success">+10 este mes</div>

                        </div>

                    </div>

                    <!-- Card 2 -->
                    <div class="col-lg-4 col-md-6 col-sm-12">

                        <div class="rounded-4 bg-white shadow p-4">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-5">Total Categorias</span>
                                <i class="fa fa-tag fs-5 color-primary"></i>
                            </div>

                            <div class="fw-bold fs-4">{{ $activeCategories }}</div>
                            <div class="text-success">+20 este mes</div>

                        </div>

                    </div>

                    <!-- Card 3 -->
                    <div class="col-lg-4 col-md-6 col-sm-12">

                        <div class="rounded-4 bg-white shadow p-4">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-5">Ventas</span>
                                <i class="fa fa-euro-sign fs-5 color-primary"></i>
                            </div>

                            <div class="fw-bold fs-4">
                                <i class="fa fa-euro-sign fs-4"></i>    
                                {{ number_format($monthlySales, 2) }}
                            </div>
                            <div class="text-success">+5.2% desde el mes pasado</div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-12 mt-5">

                <h3 class="my-4 fw-bold">
                    <i class="fas fa-link me-2 color-primary"></i>
                    Acciones rápidas
                </h3>

                <div class="row g-4">

                    <!-- Crear Categoria -->
                    <div class="col-lg-3 col-md-6 col-sm-12">

                        <a href="{{ route('categories.index') }}" class="text-decoration-none">
                            
                            <div class="rounded-4 bg-white shadow bg-hover-primary p-4 text-center h-100">
                                
                                <div class="mb-2">
                                    <i class="fas fa-tag fa-2x icon-quick"></i>
                                </div>

                                <div class="fw-bold fs-5 text-dark">Categorias</div>

                            </div>
                        </a>

                    </div>

                    <!-- Crear Producto -->
                    <div class="col-lg-3 col-md-6 col-sm-12">

                        <a href="{{ route('products.index') }}" class="text-decoration-none">
                            
                            <div class="rounded-4 bg-white bg-hover-primary shadow p-4 text-center h-100">
                                
                                <div class="mb-2">
                                    <i class="fas fa-box fa-2x icon-quick"></i>
                                </div>

                                <div class="fw-bold fs-5 text-dark">Productos</div>

                            </div>

                        </a>

                    </div>

                    <!-- Crear Venta -->
                    <div class="col-lg-3 col-md-6 col-sm-12">

                        <a href="{{ route('sales.index') }}" class="text-decoration-none">
                            
                            <div class="rounded-4 bg-white shadow bg-hover-primary p-4 text-center h-100">
                                
                                <div class="mb-2">
                                    <i class="fas fa-shopping-cart fa-2x icon-quick"></i>
                                </div>

                                <div class="fw-bold fs-5 text-dark">Ventas</div>

                            </div>

                        </a>

                    </div>

                    <!-- Ver Reportes -->
                    <div class="col-lg-3 col-md-6 col-sm-12">

                        <a href="{{ route('reports.index') }}" class="text-decoration-none">

                            <div class="rounded-4 bg-white shadow bg-hover-primary p-4 text-center h-100">
                                
                                <div class="mb-2">
                                    <i class="fas fa-chart-line fa-2x icon-quick"></i>
                                </div>

                                <div class="fw-bold fs-5 text-dark ">Reportes</div>

                            </div>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
