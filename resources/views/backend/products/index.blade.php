@extends('backend.layouts.main')

@section('title', 'Productos')

@push('scripts')
    @vite(['resources/js/modules/products.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'products.index',
                    'icon' => 'fas fa-box',
                    'label' => 'Listado de Productos'
                ]
            ])
        @endpush

        <div class="card p-4 section-hero">

            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">

                <div class="section-hero-icon">
                    <i class="fas fa-box"></i>
                </div>

                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-0">Gestión de Productos</h2>
                    <div class="text-muted small fw-bold">Controla tu inventario y precios de venta.</div>
                </div>

                <div class="d-flex flex-wrap gap-2 section-hero-actions">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal" data-bs-mode="new">
                        <i class="fas fa-plus me-1"></i> Nuevo Producto
                    </button>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar">
                <div class="section-search">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control form-control-sm" id="productsSearch" placeholder="Buscar producto...">
                </div>
                <select class="form-select form-select-sm section-filter" id="productsCategoryFilter">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">

                <table class="table table-borderless align-middle section-table">

                    <thead>

                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th class="text-center">Impuesto</th>
                            <th class="text-center">Cantidad</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        
                    </thead>

                    <tbody>

                        @if ($products->isEmpty())

                            <tr>
                                <td colspan="7" class="text-center text-muted fw-bold fs-6 my-3">No hay productos registrados.</td>
                            </tr>

                        @endif

                        @foreach($products as $product)

                            <tr data-id="{{ $product->id }}" data-category="{{ $product->category_id }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="section-avatar" style="background: {{ $product->category->color }};">
                                            <i class="fa {{ $product->category->icon }} text-white"></i>
                                        </span>
                                        <div>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            <div class="text-muted small">{{ $product->category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>€ {{ number_format($product->sale_price, 2) }}</td>
                                <td class="text-center">
                                    {{ $product->tax?->rate ? $product->tax->rate . ' %' : '-' }}
                                </td>
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td>
                                    @if ($product->status == \App\Models\Product::ACTIVE)
                                        <span class="status-pill status-pill-success">Activo</span>
                                    @else
                                        <span class="status-pill status-pill-muted">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">

                                    <button type="button" class="btn btn-icon text-primary" onclick="editProduct('{{ $product->id }}')" data-bs-mode="edit" title="Editar" 
                                    {{ $product->status == \App\Models\Product::INACTIVE ? 'disabled' : '' }}>
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if ($product->status == \App\Models\Product::ACTIVE)

                                        <button type="button" class="btn btn-icon text-danger" title="Eliminar" onclick="deleteProduct('{{ $product->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    @else

                                        <button type="button" class="btn btn-icon text-success" title="Producto Inactivo" onclick="activateProduct('{{ $product->id }}')">
                                            <i class="fas fa-check"></i>
                                        </button>

                                    @endif  

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            <div class="section-footer">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
                
        </div>

        <!-- Modal Crear/Editar Producto -->
        @include('backend.products._product_modal', ['taxes' => $taxes])

    </div>  

@endsection
