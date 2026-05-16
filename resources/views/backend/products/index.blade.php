@extends('backend.layouts.main')

@section('title', 'Productos')

@push('styles')
    @vite(['resources/css/modules/products.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/products.js'])
@endpush

@section('content')

    <div class="container-fluid p-4 products-page">

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

            <div class="products-hero-layout">

                <div class="section-hero-icon">
                    <i class="fas fa-box"></i>
                </div>

                <div class="flex-grow-1 products-hero-copy">
                    <h2 class="fw-bold mb-0">Gestión de Productos</h2>
                    <div class="text-muted small fw-bold">Controla tu inventario y precios de venta.</div>
                </div>

                <div class="d-flex flex-wrap gap-2 section-hero-actions products-hero-actions">
                    <button class="btn btn-success btn-sm products-hero-button" data-bs-toggle="modal" data-bs-target="#productModal" data-bs-mode="new">
                        <i class="fas fa-plus me-1"></i> Nuevo Producto
                    </button>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar products-toolbar">
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

            {{-- Tabla desktop (lg+) --}}
            <div class="d-none d-lg-block">
                <div class="table-responsive">

                    <table class="table table-borderless align-middle section-table">

                        <thead>

                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-center">Tallas</th>
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
                                    <td colspan="8">
                                        <div class="sd-empty-state">
                                            <span class="sd-empty-icon">
                                                <i class="fas fa-box-open"></i>
                                            </span>
                                            <p class="sd-empty-title">Sin productos registrados</p>
                                            <p class="sd-empty-desc">Añade tu primer producto para comenzar a gestionar el inventario.</p>
                                            <button class="btn btn-sm btn-success px-4" data-bs-toggle="modal" data-bs-target="#productModal" data-bs-mode="new">
                                                <i class="fas fa-plus me-1"></i> Nuevo Producto
                                            </button>
                                        </div>
                                    </td>
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
                                                <div class="mt-1">
                                                    <span class="table-chip table-chip-code">{{ $product->code }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="table-chip table-chip-category">{{ $product->category->name }}</span></td>
                                    <td class="text-center">
                                        @if ($product->has_sizes)
                                            <span class="status-pill status-pill-success">Con tallas</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Sin tallas</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->has_sizes)
                                            <span class="text-muted">-</span>
                                        @else
                                            $ {{ number_format((float) $product->sale_price, 2, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $product->tax?->rate ? $product->tax->rate . ' %' : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($product->has_sizes)
                                            <span class="text-muted">-</span>
                                        @else
                                            {{ $product->quantity }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->status == \App\Models\Product::ACTIVE)
                                            <span class="status-pill status-pill-success">Activo</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">

                                        <button type="button" class="btn btn-icon text-success" onclick="showProductDetails('{{ $product->id }}')" title="Ver detalles">
                                            <i class="fas fa-circle-info"></i>
                                        </button>

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
            </div>

            {{-- Card Slider (móvil / tablet < lg) --}}
            <div class="d-lg-none prod-slider-wrapper">

                @if($products->isEmpty())
                    <div class="p-4">
                        <div class="sd-empty-state">
                            <span class="sd-empty-icon">
                                <i class="fas fa-box-open"></i>
                            </span>
                            <p class="sd-empty-title">Sin productos registrados</p>
                            <p class="sd-empty-desc">Añade tu primer producto para comenzar a gestionar el inventario.</p>
                            <button class="btn btn-sm btn-success px-4" data-bs-toggle="modal" data-bs-target="#productModal" data-bs-mode="new">
                                <i class="fas fa-plus me-1"></i> Nuevo Producto
                            </button>
                        </div>
                    </div>
                @else

                    <div class="prod-slider" id="productSlider">
                        @foreach($products as $product)
                        <div class="prod-slide"
                             data-id="{{ $product->id }}"
                             data-category="{{ $product->category_id }}">
                            <div class="prod-card">

                                <div class="prod-card-header">
                                    <span class="section-avatar flex-shrink-0" style="background: {{ $product->category->color }};">
                                        <i class="fa {{ $product->category->icon }} text-white"></i>
                                    </span>
                                    <div class="ms-2 overflow-hidden">
                                        <div class="fw-bold text-truncate">{{ $product->name }}</div>
                                        <div class="mt-1">
                                            <span class="table-chip table-chip-code">{{ $product->code }}</span>
                                        </div>
                                    </div>
                                    <div class="ms-auto ps-2 flex-shrink-0">
                                        @if ($product->status == \App\Models\Product::ACTIVE)
                                            <span class="status-pill status-pill-success">Activo</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Inactivo</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="prod-card-stats">
                                    <div class="prod-card-stat">
                                        <span class="text-muted small">Categoría</span>
                                        <span class="table-chip table-chip-category">{{ $product->category->name }}</span>
                                    </div>
                                    <div class="prod-card-stat">
                                        <span class="text-muted small">Precio</span>
                                        <span class="fw-bold">
                                            @if ($product->has_sizes)
                                                <span class="status-pill status-pill-success">Con tallas</span>
                                            @else
                                                $ {{ number_format((float) $product->sale_price, 2, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="prod-card-stat">
                                        <span class="text-muted small">Cantidad</span>
                                        <span class="fw-bold">
                                            @if ($product->has_sizes) — @else {{ $product->quantity }} @endif
                                        </span>
                                    </div>
                                    <div class="prod-card-stat">
                                        <span class="text-muted small">IVA</span>
                                        <span class="fw-bold">{{ $product->tax?->rate ? $product->tax->rate . ' %' : '-' }}</span>
                                    </div>
                                </div>

                                <div class="prod-card-actions">
                                    <button type="button" class="btn btn-outline-success btn-sm flex-fill"
                                            onclick="showProductDetails('{{ $product->id }}')"
                                            aria-label="Ver detalles de {{ $product->name }}">
                                        <i class="fas fa-circle-info me-1"></i> Detalles
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm flex-fill"
                                            onclick="editProduct('{{ $product->id }}')"
                                            aria-label="Editar {{ $product->name }}"
                                            {{ $product->status == \App\Models\Product::INACTIVE ? 'disabled' : '' }}>
                                        <i class="fas fa-edit me-1"></i> Editar
                                    </button>
                                    @if ($product->status == \App\Models\Product::ACTIVE)
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteProduct('{{ $product->id }}')"
                                                aria-label="Desactivar {{ $product->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                onclick="activateProduct('{{ $product->id }}')"
                                                aria-label="Activar {{ $product->name }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($products->count() > 1)
                    <div class="prod-slider-dots" id="productSliderDots">
                        @foreach($products as $product)
                        <button class="prod-dot" data-index="{{ $loop->index }}" aria-label="Ir a {{ $product->name }}"></button>
                        @endforeach
                    </div>
                    @endif

                @endif

            </div>

            <div class="section-footer">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
                
        </div>

        <!-- Modal Crear/Editar Producto -->
        @include('backend.products._product_modal', ['taxes' => $taxes])
        @include('backend.products._product_details_modal')

    </div>  

@endsection
