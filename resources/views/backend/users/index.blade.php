@extends('backend.layouts.main')

@section('title', 'Gestión de Usuario')

@push('styles')
    @vite(['resources/css/modules/users.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/users.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

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

            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">

                <div class="section-hero-icon">
                    <i class="fas fa-users"></i>
                </div>

                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-0">Gestión de Usuarios</h2>
                    <div class="text-muted small fw-bold">Administra la información y roles de tu equipo.</div>
                </div>

            </div>

        </div>

        <div class="card p-0 mt-4 section-card">

            <div class="section-toolbar">
                <div class="section-search">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control form-control-sm" id="usersSearch" placeholder="Buscar usuario...">
                </div>
                <select class="form-select form-select-sm section-filter" id="usersRoleFilter">
                    <option value="">Todos los roles</option>
                    <option value="1">Super Admin</option>
                    <option value="2">Admin</option>
                    <option value="3">Cajero</option>
                </select>
            </div>
            
            <!-- Tabla de Usuarios -->
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
                                    <div class="text-muted small">{{ $user->username }}</div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td class="text-center">
                                    {{ $user->rol == 1 ? 'Super Admin' : ($user->rol == 2 ? 'Admin' : 'Cajero') }}
                                </td>
                                <td>
                                    @if($user->status == \App\Models\User::ACTIVE)
                                        <span class="status-pill status-pill-success">Activo</span>
                                    @else
                                        <span class="status-pill status-pill-muted">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">

                                    <a href="{{ route('users.info', $user->id) }}" class="btn btn-icon btn-icon-edit" title="Editar">
                                         <i class="fas fa-edit mt-1"></i>
                                    </a>

                                    @if($user->status == \App\Models\User::ACTIVE)
                                        
                                        <button class="btn btn-icon text-danger" data-id="{{ $user->id }}" onclick="deleteUser(this.getAttribute('data-id'))">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    @else

                                        <button class="btn btn-icon text-success" data-id="{{ $user->id }}" onclick="activateUser(this.getAttribute('data-id'))">
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

    </div>

@endsection
