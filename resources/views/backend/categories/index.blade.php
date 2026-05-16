@extends('backend.layouts.main')


@section('title', 'Categorías')


@push('styles')
    @vite(['resources/css/modules/categories.css'])
@endpush


@push('scripts')
    @vite(['resources/js/modules/categories.js'])
@endpush


@section('content')

    <div class="container-fluid p-4 categories-page">

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

            <div class="categories-hero-layout">

                <div class="section-hero-icon">
                    <i class="fas fa-tags"></i>
                </div>

                <div class="flex-grow-1 categories-hero-copy">
                    <h2 class="fw-bold mb-0">Gestión de Categorías</h2>
                    <div class="text-muted small fw-bold">Organiza y administra tus familias de productos eficientemente.</div>
                </div>

                <div class="d-flex flex-wrap gap-2 section-hero-actions categories-hero-actions">
                    <button type="button" class="btn btn-success btn-sm categories-hero-button" data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="fas fa-plus me-1"></i> Nueva Categoría
                    </button>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar categories-toolbar">
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

            {{-- Tabla desktop (lg+) --}}
            <div class="d-none d-lg-block">
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
                                    <td colspan="5">
                                        <div class="sd-empty-state">
                                            <span class="sd-empty-icon">
                                                <i class="fas fa-tags"></i>
                                            </span>
                                            <p class="sd-empty-title">Sin categorías registradas</p>
                                            <p class="sd-empty-desc">Crea tu primera categoría para organizar los productos del negocio.</p>
                                            <button type="button" class="btn btn-sm btn-success px-4" data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                                <i class="fas fa-plus me-1"></i> Nueva Categoría
                                            </button>
                                        </div>
                                    </td>
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
                                                <div class="mt-1">
                                                    <span class="table-chip table-chip-abbr">{{ $category->abbreviation }}</span>
                                                </div>
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

                </div>
            </div>

            {{-- Card Slider (móvil / tablet < lg) --}}
            <div class="d-lg-none cat-slider-wrapper">

                @if($categories->isEmpty())
                    <div class="p-4">
                        <div class="sd-empty-state">
                            <span class="sd-empty-icon">
                                <i class="fas fa-tags"></i>
                            </span>
                            <p class="sd-empty-title">Sin categorías registradas</p>
                            <p class="sd-empty-desc">Crea tu primera categoría para organizar los productos del negocio.</p>
                            <button type="button" class="btn btn-sm btn-success px-4" data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="fas fa-plus me-1"></i> Nueva Categoría
                            </button>
                        </div>
                    </div>
                @else

                    <div class="cat-slider" id="categorySlider">
                        @foreach($categories as $category)
                        <div class="cat-slide"
                             data-id="{{ $category->id }}"
                             data-status="{{ $category->status == \App\Models\Category::ACTIVE ? 'active' : 'inactive' }}"
                             data-name="{{ strtolower($category->name) }}">
                            <div class="cat-card">

                                <div class="cat-card-header">
                                    <span class="section-avatar flex-shrink-0" style="background: {{ $category->color }};">
                                        <i class="fa {{ $category->icon }} text-white"></i>
                                    </span>
                                    <div class="ms-2 overflow-hidden">
                                        <div class="fw-bold text-truncate">{{ $category->name }}</div>
                                        <div class="mt-1">
                                            <span class="table-chip table-chip-abbr">{{ $category->abbreviation }}</span>
                                        </div>
                                    </div>
                                    <div class="ms-auto ps-2 flex-shrink-0">
                                        @if($category->status == \App\Models\Category::ACTIVE)
                                            <span class="status-pill status-pill-success">Activo</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Inactivo</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="cat-card-stat">
                                    <span class="text-muted small">Total Productos</span>
                                    <span class="fw-bold">{{ $category->products_count ?? '-' }}</span>
                                </div>

                                <div class="cat-card-actions">
                                    <button onclick="editCategory('{{ $category->id }}')"
                                            class="btn btn-outline-primary btn-sm flex-fill"
                                            aria-label="Editar categoría {{ $category->name }}"
                                            {{ $category->status == \App\Models\Category::INACTIVE ? 'disabled' : '' }}>
                                        <i class="fas fa-edit me-1"></i> Editar
                                    </button>
                                    @if($category->status == \App\Models\Category::ACTIVE)
                                        <button class="btn btn-outline-danger btn-sm flex-fill"
                                                onclick="deleteCategory('{{ $category->id }}')"
                                                aria-label="Desactivar categoría {{ $category->name }}">
                                            <i class="fas fa-trash me-1"></i> Desactivar
                                        </button>
                                    @else
                                        <button class="btn btn-outline-success btn-sm flex-fill"
                                                onclick="activateCategory('{{ $category->id }}')"
                                                aria-label="Activar categoría {{ $category->name }}">
                                            <i class="fas fa-check me-1"></i> Activar
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($categories->count() > 1)
                    <div class="cat-slider-dots" id="categorySliderDots">
                        @foreach($categories as $category)
                        <button class="cat-dot" data-index="{{ $loop->index }}" aria-label="Ir a {{ $category->name }}"></button>
                        @endforeach
                    </div>
                    @endif

                @endif

            </div>

            <!-- Modal Crear/Editar Categoría -->
            @include('backend.categories._category_modal')

            <div class="section-footer">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

@endsection
