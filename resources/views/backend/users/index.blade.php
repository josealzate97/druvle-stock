@extends('backend.layouts.main')

@section('title', 'Gestión de Usuario')

@push('styles')
    @vite(['resources/css/modules/users.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/users.js'])
@endpush

@section('content')

    <div class="container-fluid p-4 users-page">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'users.index',
                    'icon' => 'fas fa-users',
                    'label' => 'Gestión de Usuario'
                ]
            ])
        @endpush

        <div class="card p-4 section-hero">

            <div class="users-hero-layout">

                <div class="section-hero-icon">
                    <i class="fas fa-users"></i>
                </div>

                <div class="flex-grow-1 users-hero-copy">
                    <h2 class="fw-bold mb-0">Gestión de Usuarios</h2>
                    <div class="text-muted small fw-bold">Administra la información y roles de tu equipo.</div>
                </div>

                <div class="d-flex flex-wrap gap-2 section-hero-actions users-hero-actions">
                    <a href="{{ route('users.info') }}" class="btn btn-success btn-sm users-hero-button">
                        <i class="fas fa-plus me-1"></i> Nuevo Usuario
                    </a>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar users-toolbar">
                <div class="section-search">
                    <i class="fas fa-search"></i>
                    <label class="visually-hidden" for="usersSearch">Buscar usuario</label>
                    <input type="text" class="form-control form-control-sm" id="usersSearch" placeholder="Buscar usuario...">
                </div>
                <label class="visually-hidden" for="usersRoleFilter">Filtrar por rol</label>
                <select class="form-select form-select-sm section-filter" id="usersRoleFilter">
                    <option value="">Todos los roles</option>
                    <option value="1">Super Admin</option>
                    <option value="2">Admin</option>
                    <option value="3">Cajero</option>
                </select>
            </div>
            
            {{-- Tabla desktop (lg+) --}}
            <div class="d-none d-lg-block">
                <div class="table-responsive">

                    <table class="table table-borderless align-middle section-table">

                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th class="text-center">Rol</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($users as $user)

                                <tr data-id="{{ $user->id }}" data-role="{{ $user->rol }}">
                                    <td>
                                        <div class="fw-bold">{{ $user->name }} {{ $user->lastname }}</div>
                                        <div class="mt-1">
                                            <span class="table-chip table-chip-user">{{ $user->username }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td class="text-center">
                                        <span class="role-badge role-badge-purple">
                                            {{ $user->rol == 1 ? 'Super Admin' : ($user->rol == 2 ? 'Admin' : 'Cajero') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->status == \App\Models\User::ACTIVE)
                                            <span class="status-pill status-pill-success">Activo</span>
                                        @else
                                            <span class="status-pill status-pill-muted">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">

                                                                                <a href="{{ route('users.info', $user->id) }}" class="btn btn-icon btn-icon-warning-outline"
                                           aria-label="Editar usuario {{ $user->name }} {{ $user->lastname }}" title="Editar usuario {{ $user->name }} {{ $user->lastname }}">
                                             <i class="fas fa-edit mt-1"></i>
                                        </a>

                                        @if($user->status == \App\Models\User::ACTIVE)
                                            
                                            <button class="btn btn-icon btn-icon-danger-outline" data-id="{{ $user->id }}"
                                                aria-label="Desactivar usuario {{ $user->name }} {{ $user->lastname }}" title="Desactivar usuario {{ $user->name }} {{ $user->lastname }}"
                                                onclick="deleteUser(this.getAttribute('data-id'))">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        @else

                                            <button class="btn btn-icon text-success" data-id="{{ $user->id }}"
                                                aria-label="Activar usuario {{ $user->name }} {{ $user->lastname }}" title="Activar usuario {{ $user->name }} {{ $user->lastname }}"
                                                onclick="activateUser(this.getAttribute('data-id'))">
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
            <div class="d-lg-none usr-slider-wrapper">

                <div class="usr-slider" id="userSlider">
                    @foreach($users as $user)
                    <div class="usr-slide"
                         data-id="{{ $user->id }}"
                         data-role="{{ $user->rol }}">
                        <div class="usr-card">

                            <div class="usr-card-header">
                                <span class="usr-avatar flex-shrink-0">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="ms-2 overflow-hidden">
                                    <div class="fw-bold text-truncate">{{ $user->name }} {{ $user->lastname }}</div>
                                    <div class="mt-1">
                                        <span class="table-chip table-chip-user">{{ $user->username }}</span>
                                    </div>
                                </div>
                                <div class="ms-auto ps-2 flex-shrink-0">
                                    @if($user->status == \App\Models\User::ACTIVE)
                                        <span class="status-pill status-pill-success">Activo</span>
                                    @else
                                        <span class="status-pill status-pill-muted">Inactivo</span>
                                    @endif
                                </div>
                            </div>

                            <div class="usr-card-stats">
                                <div class="usr-card-stat usr-card-stat--full">
                                    <span class="text-muted small"><i class="fas fa-envelope me-1"></i>Email</span>
                                    <span class="fw-bold text-truncate">{{ $user->email }}</span>
                                </div>
                                <div class="usr-card-stat">
                                    <span class="text-muted small"><i class="fas fa-phone me-1"></i>Teléfono</span>
                                    <span class="fw-bold">{{ $user->phone ?: '-' }}</span>
                                </div>
                                <div class="usr-card-stat">
                                    <span class="text-muted small"><i class="fas fa-shield-halved me-1"></i>Rol</span>
                                    <span class="role-badge role-badge-purple">
                                        {{ $user->rol == 1 ? 'Super Admin' : ($user->rol == 2 ? 'Admin' : 'Cajero') }}
                                    </span>
                                </div>
                            </div>

                            <div class="usr-card-actions">
                                <a href="{{ route('users.info', $user->id) }}"
                                   class="btn btn-outline-warning btn-sm flex-fill"
                                   aria-label="Editar usuario {{ $user->name }} {{ $user->lastname }}">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </a>
                                @if($user->status == \App\Models\User::ACTIVE)
                                    <button class="btn btn-outline-danger btn-sm" data-id="{{ $user->id }}"
                                            aria-label="Desactivar usuario {{ $user->name }} {{ $user->lastname }}"
                                            onclick="deleteUser(this.getAttribute('data-id'))">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <button class="btn btn-outline-success btn-sm" data-id="{{ $user->id }}"
                                            aria-label="Activar usuario {{ $user->name }} {{ $user->lastname }}"
                                            onclick="activateUser(this.getAttribute('data-id'))">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>

                @if($users->count() > 1)
                <div class="usr-slider-dots" id="userSliderDots">
                    @foreach($users as $user)
                    <button class="usr-dot" data-index="{{ $loop->index }}" aria-label="Ir a {{ $user->name }}"></button>
                    @endforeach
                </div>
                @endif

            </div>

        </div>

    </div>

@endsection
