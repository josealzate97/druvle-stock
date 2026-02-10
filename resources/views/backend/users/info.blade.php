@extends('backend.layouts.main')

@section('title', 'Info Usuario')

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

        <div class="card p-4" x-data="userForm({
            name: '{{ $user->name }}',
            lastname: '{{ $user->lastname }}',
            username: '{{ $user->username }}',
            phone: '{{ $user->phone }}',
            rol: '{{ $user->rol }}',
            password: '{{ $user->password }}',
            showPassword: false,
            email: '{{ $user->email }}',
            id: '{{ $user->id }}',
            status: '{{ $user->status }}',
        })">


            <div class="d-flex align-items-center col-12">

                <h3 class="fw-bold">
                    <i class="fa fa-user me-2 color-primary"></i>
                    Información del Usuario - {{ $user->name }} {{ $user->lastname }}
                </h3>

                <button class="btn btn-warning ms-auto" @click="toggleEdit">
                    <i class="fa fa-edit"></i> <span x-text="editMode ? 'Cancelar' : 'Editar'"></span>
                </button>

            </div>
            
            <hr>

            <form class="form" @submit.prevent="saveUser">

                <div class="row g-4">

                    <input type="hidden" name="status" x-model="form.status">

                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <label class="form-label fw-bold">Nombres</label>
                        <input type="text" class="form-control" x-model="form.name" :disabled="!editMode">
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" class="form-control" x-model="form.lastname" :disabled="!editMode">
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <label class="form-label fw-bold">Usuario</label>
                        <input type="text" class="form-control" x-model="form.username" :disabled="!editMode">
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" class="form-control" x-model="form.phone" :disabled="!editMode">
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-12" x-show="editMode">
                        <label class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" class="form-control" x-model="form.new_password" @change="validatePassword" :disabled="!editMode">
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <label class="form-label fw-bold">Rol</label>
                        <select class="form-control" x-model="form.rol" :disabled="!editMode">
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <label class="form-label fw-bold">Correo electrónico</label>
                        <input type="text" class="form-control" x-model="form.email" :disabled="!editMode">
                    </div>

                </div>

                <div class="my-5 text-center" x-show="editMode">
                    <button type="submit" class="btn btn-success btn-lg col-4 fw-bold">
                        <i class="fa fa-save"></i>&nbsp;
                        Actualizar Informacion
                    </button>
                </div>

            </form>

        </div>

    </div>

@endsection
