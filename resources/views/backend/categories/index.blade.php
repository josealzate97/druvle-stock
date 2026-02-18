@extends('backend.layouts.main')


@section('title', 'Categorías')


@push('scripts')
    @vite(['resources/js/modules/categories.js'])
@endpush


@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'categories.index',
                    'icon' => 'fas fa-tags',
                    'label' => 'Listado de Categorías'
                ]
            ])
        @endpush

        <div class="card p-4 section-hero">

            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">

                <div class="section-hero-icon">
                    <i class="fas fa-tags"></i>
                </div>

                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-0">Gestión de Categorías</h2>
                    <div class="text-muted small fw-bold">Organiza y administra tus familias de productos eficientemente.</div>
                </div>

                <div class="d-flex flex-wrap gap-2 section-hero-actions">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="fas fa-plus me-1"></i> Nueva Categoría
                    </button>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar">
                <div class="section-search">
                    <i class="fas fa-search"></i>
                    <label class="visually-hidden" for="categoriesSearch">Buscar categoría</label>
                    <input type="text" class="form-control form-control-sm" id="categoriesSearch" placeholder="Buscar categoría...">
                </div>
                <label class="visually-hidden" for="categoriesStatusFilter">Filtrar por estado</label>
                <select class="form-select form-select-sm section-filter" id="categoriesStatusFilter">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                </select>
            </div>

            <div class="table-responsive">

                <table class="table table-borderless align-middle section-table">

                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th class="text-center">Total Productos</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @if ($categories->isEmpty())

                            <tr>
                                <td colspan="5" class="text-center text-muted fw-bold fs-6 my-3">No hay categorías registradas.</td>
                            </tr>

                        @endif

                        @foreach($categories as $category)

                            <tr data-id="{{ $category->id }}" data-status="{{ $category->status == \App\Models\Category::ACTIVE ? 'active' : 'inactive' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="section-avatar" style="background: {{ $category->color }};">
                                            <i class="fa {{ $category->icon }} text-white"></i>
                                        </span>
                                        <div>
                                            <div class="fw-bold">{{ $category->name }}</div>
                                            <div class="text-muted small">{{ $category->abbreviation }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted text-center">{{ $category->products_count ?? '-' }}</td>
                                <td>
                                    @if($category->status == \App\Models\Category::ACTIVE)
                                        <span class="status-pill status-pill-success">Activo</span>
                                    @else
                                        <span class="status-pill status-pill-muted">Inactivo</span>
                                    @endif
                                </td>

                                <td class="text-end">

                                    <button onclick="editCategory('{{ $category->id }}')" class="btn btn-icon text-primary" data-bs-mode="edit"
                                    aria-label="Editar categoría {{ $category->name }}" title="Editar categoría {{ $category->name }}"
                                    {{ $category->status == \App\Models\Category::INACTIVE ? 'disabled' : '' }}>
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if($category->status == \App\Models\Category::ACTIVE)
                                        
                                        <button class="btn btn-icon text-danger" onclick="deleteCategory('{{ $category->id }}')"
                                        aria-label="Desactivar categoría {{ $category->name }}" title="Desactivar categoría {{ $category->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    @else
                                        <button class="btn btn-icon text-success" onclick="activateCategory('{{ $category->id }}')"
                                        aria-label="Activar categoría {{ $category->name }}" title="Activar categoría {{ $category->name }}">
                                            <i class="fas fa-check"></i>
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

            <div class="section-footer">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

@endsection
