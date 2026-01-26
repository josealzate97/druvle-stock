@extends('backend.layouts.main')

@section('title', 'Gestión de Usuario')

@push('scripts')
    @vite(['resources/js/modules/users.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @include('backend.components.breadcrumb', [
            'section' => [
                'route' => 'users.index',
                'icon' => 'fas fa-users',
                'label' => 'Gestión de Usuario'
            ]
        ])

        <div class="card p-4">

            <h3 class="fw-bold mb-2">
                <i class="fa fa-list me-2 color-primary"></i>
                Gestión de Usuario
            </h3>

            <div class="text-muted fw-bold small">Aqui podras gestionar la información de tus empleados</div>

            <hr>
            
            <!-- Tabla de Usuarios -->
            <div class="table-responsive mt-4">

                <table class="table table-borderless align-middle table-striped table-hover">

                    <thead class="table-light">
                        <tr>
                            <th class="color-primary">Nombre</th>
                            <th class="color-primary">Apellido</th>
                            <th class="color-primary">Usuario</th>
                            <th class="color-primary">Email</th>
                            <th class="color-primary">Teléfono</th>
                            <th class="color-primary text-center">Rol</th>
                            <th class="color-primary">Status</th>
                            <th class="color-primary text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($users as $user)

                            <tr data-id="{{ $user->id }}">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->lastname }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td class="text-center">
                                    {{ $user->rol == 1 ? 'Super Admin' : ($user->rol == 2 ? 'Admin' : 'Cajero') }}
                                </td>
                                <td>
                                    @if($user->status == \App\Models\User::ACTIVE)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">

                                    <a href="{{ route('users.info', $user->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </a>

                                    @if($user->status == \App\Models\User::ACTIVE)
                                        
                                        <button class="btn btn-sm btn-danger" data-id="{{ $user->id }}" onclick="deleteUser(this.getAttribute('data-id'))">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>

                                    @else

                                        <button class="btn btn-sm btn-success" data-id="{{ $user->id }}" onclick="activateUser(this.getAttribute('data-id'))">
                                            <i class="fas fa-check"></i> Activar
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