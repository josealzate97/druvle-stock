@extends('backend.layouts.main')

@section('title', 'Productos')

@push('scripts')
    @vite(['resources/js/modules/products.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @include('backend.components.breadcrumb', [
            'section' => [
                'route' => 'products.index',
                'icon' => 'fas fa-box',
                'label' => 'Listado de Productos'
            ]
        ])

        <div class="card p-4">

            <div class="row align-items-center">

                <div class="col-lg-8 col-md-6 col-sm-12">

                    <h2 class="fw-bold mb-0">
                        <i class="fa fa-box me-2 color-primary"></i>
                        Listado Productos
                    </h2>

                    <div class="text-muted fw-bold small">Gestiona tu inventario de productos.</div>
                
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 d-flex justify-content-md-end align-items-center mt-md-0">
                    
                    <!-- Botón Crear Producto -->
                    <button class="btn btn-success mb-3 mt-3 col-lg-8 col-md-8 col-sm-12" data-bs-toggle="modal" data-bs-target="#productModal" data-bs-mode="new">
                        <i class="fas fa-plus"></i> Crear Producto
                    </button>
                    
                </div>

            </div>

            <hr>

            <div class="col-12 mt-3">

                <div class="table-responsive">

                    <table class="table table-borderless align-middle table-striped table-hover">

                        <thead class="table-light">

                            <tr>
                                <th class="color-primary">Nombre</th>
                                <th class="color-primary">Categoría</th>
                                <th class="color-primary">Precio</th>
                                <th class="color-primary text-center">Impuesto</th>
                                <th class="color-primary text-center">Cantidad</th>
                                <th class="color-primary text-center">Estado</th>
                                <th class="color-primary text-center">Acciones</th>
                            </tr>
                            
                        </thead>

                        <tbody>

                            @if ($products->isEmpty())

                                <tr>
                                    <td colspan="7" class="text-center text-muted fw-bold fs-5 my-3">No hay productos registrados.</td>
                                </tr>

                            @endif

                            @foreach($products as $product)

                                <tr data-id="{{ $product->id }}">
                                    <td>
                                        <span class="d-inline-flex align-items-center justify-content-center me-2 rounded-3"
                                        style="background: {{ $product->category->color }}; width: 30px; height: 30px;">
                                            <i class="fa {{ $product->category->icon }} text-white"></i>
                                        </span>

                                        {{ $product->name }}
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>€ {{ number_format($product->sale_price, 2) }}</td>
                                    <td class="text-center">
                                        {{ $product->tax?->rate ? $product->tax->rate . ' %' : '-' }}
                                    </td>
                                    <td class="text-center">{{ $product->quantity }}</td>
                                    <td class="text-center">

                                        @if ($product->status == \App\Models\Product::ACTIVE)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="gap-5 text-center">

                                        <button type="button" class="btn btn-primary" onclick="editProduct('{{ $product->id }}')" data-bs-mode="edit" title="Editar" 
                                        {{ $product->status == \App\Models\Product::INACTIVE ? 'disabled' : '' }}>
                                            <i class="fas fa-edit"></i>
                                            Editar
                                        </button>

                                        @if ($product->status == \App\Models\Product::ACTIVE)

                                            <button type="button" class="btn btn-danger" title="Eliminar" onclick="deleteProduct('{{ $product->id }}')">
                                                <i class="fas fa-trash"></i>
                                                Eliminar
                                            </button>

                                        @else

                                            <button type="button" class="btn btn-success" title="Producto Inactivo" onclick="activateProduct('{{ $product->id }}')">
                                                <i class="fas fa-check"></i>
                                                Activar
                                            </button>

                                        @endif  

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{ $products->links('pagination::bootstrap-5') }}
                
            </div>

        </div>

        <!-- Modal Crear/Editar Producto -->
        @include('backend.products._product_modal', ['taxes' => $taxes])

    </div>  

@endsection