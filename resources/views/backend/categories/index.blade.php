@extends('backend.layouts.main')


@section('title', 'Categorías')


@push('scripts')
    @vite(['resources/js/modules/categories.js'])
@endpush


@section('content')

    <div class="container-fluid p-4">

        @include('backend.components.breadcrumb', [
            'section' => [
                'route' => 'categories.index',
                'icon' => 'fas fa-tags',
                'label' => 'Listado de Categorías'
            ]
        ])

        <div class="card p-4">

            <div class="col-12">

                <div class="row align-items-center">

                    <div class="col-lg-8 col-md-8 col-sm-12 d-flex flex-column">
                        <h2 class="fw-bold mb-0">
                            <i class="fas fa-tags me-2 color-primary"></i>
                            Categorías
                        </h2>
                        <div class="text-muted fw-bold small">Gestiona las categorías de tus productos</div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 d-flex justify-content-md-end align-items-center mt-md-0">
                        <!-- Botón Crear Categoría -->
                        <button type="button" class="btn btn-success mb-3 mt-3 col-lg-8 col-md-8 col-sm-12" data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#categoryModal">
                            <i class="fas fa-plus"></i> Crear Categoría
                        </button>
                    </div>

                </div>

            </div>

            <hr>

            <div class="col-12">

                <div class="table-responsive">

                    <table class="table table-borderless align-middle table-striped table-hover">

                        <thead class="table-light">
                            <tr>
                                <th class="color-primary fw-bold">Nombre</th>
                                <th class="color-primary fw-bold">Abreviación</th>
                                <th class="color-primary fw-bold">Estado</th>
                                <th class="color-primary fw-bold text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @if ($categories->isEmpty())

                                <tr>
                                    <td colspan="4" class="text-center text-muted fw-bold fs-5 my-3">No hay categorías registradas.</td>
                                </tr>

                            @endif

                            @foreach($categories as $category)

                                <tr data-id="{{ $category->id }}">
                                    <td>
                                        <span class="d-inline-flex align-items-center justify-content-center me-2 rounded-3"
                                        style="background: {{ $category->color }}; width: 30px; height: 30px;">
                                            <i class="fa {{ $category->icon }} text-white"></i>
                                        </span>
                                        {{ $category->name }}
                                    </td>
                                    <td>{{ $category->abbreviation }}</td>
                                    <td>
                                        @if($category->status == \App\Models\Category::ACTIVE)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        
                                        <button onclick="editCategory('{{ $category->id }}')" class="btn btn-sm btn-primary" data-bs-mode="edit"
                                        {{ $category->status == \App\Models\Category::INACTIVE ? 'disabled' : '' }}>
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                         @if($category->status == \App\Models\Category::ACTIVE)
                                            
                                            <button class="btn btn-sm btn-danger" onclick="deleteCategory('{{ $category->id }}')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>

                                        @else
                                            <button class="btn btn-sm btn-success" onclick="activateCategory('{{ $category->id }}')">
                                                <i class="fas fa-check"></i> Activar
                                            </button>
                                        @endif

                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                    <!-- Modal Crear/Editar Categoría -->
                    @include('backend.categories._category_modal')

                </div>

                {{ $categories->links('pagination::bootstrap-5') }}

            </div>

        </div>

    </div>

@endsection