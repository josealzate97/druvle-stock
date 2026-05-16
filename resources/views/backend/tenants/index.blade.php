@extends('backend.layouts.main')

@section('title', 'Negocios')

@push('styles')
    @vite(['resources/css/modules/tenants.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/tenants.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'tenants.index',
                    'icon'  => 'fas fa-building',
                    'label' => 'Listado de Negocios'
                ]
            ])
        @endpush

        <div class="card p-4 section-hero tenants-hero">

            <div class="tenants-hero__row">

                <div class="section-hero-icon tenants-hero__icon">
                    <i class="fas fa-building"></i>
                </div>

                <div class="flex-grow-1 tenants-hero__content">
                    <h2 class="fw-bold mb-0 tenants-hero__title">Gestión de Negocios</h2>
                    <div class="text-muted small fw-bold tenants-hero__subtitle">Administra los negocios registrados en la plataforma.</div>
                </div>

                <div class="d-flex flex-wrap gap-2 section-hero-actions tenants-hero__actions">
                    <button type="button" class="btn btn-success btn-sm tenants-hero__add-btn" data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#tenantModal">
                        <i class="fas fa-plus me-1"></i> Nuevo Negocio
                    </button>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar tenants-toolbar">
                <div class="section-search tenants-toolbar__search">
                    <i class="fas fa-search"></i>
                    <label class="visually-hidden" for="tenantsSearch">Buscar negocio</label>
                    <input type="text" class="form-control form-control-sm" id="tenantsSearch" placeholder="Buscar negocio...">
                </div>
                <div class="tenants-toolbar__status">
                    <label class="visually-hidden" for="tenantsStatusFilter">Filtrar por estado</label>
                    <select class="form-select form-select-sm section-filter" id="tenantsStatusFilter">
                        <option value="">Todos los estados</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
            </div>

            {{-- Tabla desktop (lg+) --}}
            <div class="d-none d-lg-block">
                <div class="table-responsive">

                    <table class="table table-borderless align-middle section-table">

                        <thead>
                            <tr>
                                <th>Negocio</th>
                                <th>Plan</th>
                                <th>Prueba hasta</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @if ($tenants->isEmpty())
                                <tr>
                                    <td colspan="5">
                                        <div class="sd-empty-state">
                                            <span class="sd-empty-icon">
                                                <i class="fas fa-store-slash"></i>
                                            </span>
                                            <p class="sd-empty-title">Sin negocios registrados</p>
                                            <p class="sd-empty-desc">Aún no hay negocios en la plataforma. Crea el primero para comenzar.</p>
                                            <button type="button" class="btn btn-sm btn-success px-4"
                                                data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#tenantModal">
                                                <i class="fas fa-plus me-1"></i> Nuevo Negocio
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            @foreach ($tenants as $tenant)

                                @php
                                    $planLabels = [1 => 'Free', 2 => 'Basic', 3 => 'Pro'];
                                    $planClasses = [1 => 'table-chip-abbr', 2 => 'table-chip-blue', 3 => 'table-chip-gold'];
                                @endphp

                                <tr data-id="{{ $tenant->id }}" data-status="{{ $tenant->status ? 'active' : 'inactive' }}">

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="section-avatar">
                                                <i class="fas fa-building text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $tenant->name }}</div>
                                                <div class="mt-1">
                                                    <span class="table-chip table-chip-abbr">{{ $tenant->slug }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="table-chip {{ $planClasses[$tenant->plan] ?? 'table-chip-abbr' }}">
                                            {{ $planLabels[$tenant->plan] ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d/m/Y') : '—' }}
                                    </td>

                                    <td>
                                        @if ($tenant->status)
                                            <span class="status-pill status-pill-success">Activo</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="text-end">

                                        <form action="{{ route('tenants.switch', $tenant->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-icon btn-icon-purple"
                                                aria-label="Entrar al negocio {{ $tenant->name }}"
                                                title="Entrar al negocio {{ $tenant->name }}"
                                                {{ !$tenant->status ? 'disabled' : '' }}>
                                                <i class="fas fa-sign-in-alt"></i>
                                            </button>
                                        </form>

                                        <button onclick="editTenant('{{ $tenant->id }}')"
                                            class="btn btn-icon text-primary"
                                            aria-label="Editar negocio {{ $tenant->name }}"
                                            title="Editar negocio {{ $tenant->name }}"
                                            {{ !$tenant->status ? 'disabled' : '' }}>
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if ($tenant->status)
                                            <button class="btn btn-icon text-danger"
                                                onclick="deleteTenant('{{ $tenant->id }}')"
                                                aria-label="Desactivar negocio {{ $tenant->name }}"
                                                title="Desactivar negocio {{ $tenant->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-icon text-success"
                                                onclick="activateTenant('{{ $tenant->id }}')"
                                                aria-label="Activar negocio {{ $tenant->name }}"
                                                title="Activar negocio {{ $tenant->name }}">
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
            <div class="d-lg-none tnt-slider-wrapper">

                @if($tenants->isEmpty())
                    <div class="p-4">
                        <div class="sd-empty-state">
                            <span class="sd-empty-icon">
                                <i class="fas fa-store-slash"></i>
                            </span>
                            <p class="sd-empty-title">Sin negocios registrados</p>
                            <p class="sd-empty-desc">Aún no hay negocios en la plataforma. Crea el primero para comenzar.</p>
                            <button type="button" class="btn btn-sm btn-success px-4"
                                data-bs-mode="new" data-bs-toggle="modal" data-bs-target="#tenantModal">
                                <i class="fas fa-plus me-1"></i> Nuevo Negocio
                            </button>
                        </div>
                    </div>
                @else

                    <div class="tnt-slider" id="tenantSlider">
                        @foreach ($tenants as $tenant)
                        @php
                            $planLabels  = [1 => 'Free', 2 => 'Basic', 3 => 'Pro'];
                            $planClasses = [1 => 'table-chip-abbr', 2 => 'table-chip-blue', 3 => 'table-chip-gold'];
                        @endphp
                        <div class="tnt-slide"
                             data-id="{{ $tenant->id }}"
                             data-status="{{ $tenant->status ? 'active' : 'inactive' }}">
                            <div class="tnt-card">

                                <div class="tnt-card-header">
                                    <div class="section-avatar flex-shrink-0">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div class="ms-2 overflow-hidden">
                                        <div class="fw-bold text-truncate">{{ $tenant->name }}</div>
                                        <div class="mt-1">
                                            <span class="table-chip table-chip-abbr">{{ $tenant->slug }}</span>
                                        </div>
                                    </div>
                                    <div class="ms-auto ps-2 flex-shrink-0">
                                        @if ($tenant->status)
                                            <span class="status-pill status-pill-success">Activo</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Inactivo</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="tnt-card-stats">
                                    <div class="tnt-card-stat">
                                        <span class="text-muted small fw-bold">Plan</span>
                                        <span class="table-chip {{ $planClasses[$tenant->plan] ?? 'table-chip-abbr' }}">
                                            {{ $planLabels[$tenant->plan] ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="tnt-card-stat">
                                        <span class="text-muted small fw-bold"><i class="fas fa-calendar me-1"></i>Prueba hasta</span>
                                        <span class="fw-bold">{{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d/m/Y') : '—' }}</span>
                                    </div>
                                </div>

                                <div class="tnt-card-actions">
                                    <form action="{{ route('tenants.switch', $tenant->id) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-icon-purple btn-sm w-100"
                                            aria-label="Entrar al negocio {{ $tenant->name }}"
                                            {{ !$tenant->status ? 'disabled' : '' }}>
                                            <i class="fas fa-sign-in-alt me-1"></i> Entrar
                                        </button>
                                    </form>
                                    <button onclick="editTenant('{{ $tenant->id }}')"
                                        class="btn btn-outline-primary btn-sm"
                                        aria-label="Editar negocio {{ $tenant->name }}"
                                        {{ !$tenant->status ? 'disabled' : '' }}>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if ($tenant->status)
                                        <button class="btn btn-outline-danger btn-sm"
                                            onclick="deleteTenant('{{ $tenant->id }}')"
                                            aria-label="Desactivar negocio {{ $tenant->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-success btn-sm"
                                            onclick="activateTenant('{{ $tenant->id }}')"
                                            aria-label="Activar negocio {{ $tenant->name }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($tenants->count() > 1)
                    <div class="tnt-slider-dots" id="tenantSliderDots">
                        @foreach($tenants as $tenant)
                        <button class="tnt-dot" data-index="{{ $loop->index }}" aria-label="Ir a {{ $tenant->name }}"></button>
                        @endforeach
                    </div>
                    @endif

                @endif

            </div>

            <!-- Modal Crear/Editar Negocio -->
            @include('backend.tenants._tenant_modal')

            <div class="section-footer">
                {{ $tenants->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

@endsection
