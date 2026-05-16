@extends('backend.layouts.main')

@section('title', $isCreateMode ? 'Nuevo Usuario' : 'Info Usuario')

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

        <div class="card p-4 user-info-card" x-data="userForm({
            name: '{{ $user->name }}',
            lastname: '{{ $user->lastname }}',
            username: '{{ $user->username }}',
            phone: '{{ $user->phone }}',
            rol: '{{ $user->rol }}',
            showPassword: false,
            email: '{{ $user->email }}',
            id: '{{ $user->id ?? '' }}',
            status: '{{ $user->status }}',
            mode: '{{ $isCreateMode ? 'create' : 'edit' }}',
        })">


            <div class="user-info-header">
                <div class="user-info-title">
                    <div class="user-avatar-lg">
                        <i class="fa fa-user"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1">{{ $isCreateMode ? 'Crear Usuario' : 'Información del Usuario' }}</h3>
                        <div class="text-muted fw-bold small user-info-subtitle">
                            {{ $isCreateMode ? 'Completa los datos para registrar un nuevo usuario.' : $user->name . ' ' . $user->lastname }}
                        </div>
                    </div>
                </div>

                @if($isCreateMode)
                    <a href="{{ route('users.index') }}" class="btn user-back-btn">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                @else
                    <button class="btn edit-outline-btn" @click="toggleEdit">
                        <i class="fa fa-edit"></i> <span x-text="editMode ? 'Cancelar' : 'Editar'"></span>
                    </button>
                @endif
            </div>

            <div class="user-info-divider"></div>

            <form class="form user-info-form" @submit.prevent="saveUser">

                <div class="row g-4">

                    <input type="hidden" name="status" x-model="form.status">

                    <div class="col-6">
                        <div class="user-info-section">
                            <div class="user-info-section-title">Datos personales</div>

                            <div class="row g-3 mt-1">
                                <div class="col-lg-12 col-md-6 col-sm-12">
                                    <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" x-model="form.name" :disabled="!editMode" required>
                                </div>

                                <div class="col-lg-12 col-md-6 col-sm-12">
                                    <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" x-model="form.lastname" :disabled="!editMode" required>
                                </div>

                                <div class="col-lg-12 col-md-6 col-sm-12">
                                    <label class="form-label fw-bold">Usuario <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" x-model="form.username" @input="validateUsername(false)" @blur="validateUsername(true)" :disabled="!editMode" required>
                                    <small class="text-muted mt-3" x-show="isCheckingUsername">Validando usuario...</small>
                                    <small class="text-danger mt-3" x-show="form.username.trim().length > 0 && !isCheckingUsername && !isUsernameValid">Este usuario ya existe para este negocio.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="user-info-section">
                            <div class="user-info-section-title">Contacto y acceso</div>

                            <div class="row g-3 mt-1">
                                <div class="col-lg-12 col-md-6 col-sm-12">
                                    <label class="form-label fw-bold">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" x-model="form.phone" @input="applyPhoneMask" :disabled="!editMode" maxlength="12" required>
                                </div>

                                <div class="col-lg-12 col-md-6 col-sm-12">
                                    <label class="form-label fw-bold">Correo electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" x-model="form.email" @input="validateEmail()" :disabled="!editMode" required>
                                    <small class="text-danger mt-3" x-show="form.email.trim().length > 0 && !isEmailValid">Ingresa un correo válido.</small>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label class="form-label fw-bold">Rol</label>
                                    <select class="form-control" x-model="form.rol" :disabled="!editMode">
                                        @foreach($roles as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12" x-show="editMode">
                                    <label class="form-label fw-bold">
                                        <span x-text="isCreateMode ? 'Contraseña' : 'Nueva Contraseña'"></span>
                                        <template x-if="isCreateMode">
                                            <span class="text-danger">*</span>
                                        </template>
                                    </label>
                                    <input type="password" class="form-control" x-model="form.new_password" @input="validatePassword(false)" @blur="validatePassword(true)" :disabled="!editMode" :required="isCreateMode">
                                    <small class="text-danger mt-3" x-show="form.new_password.length > 0 && !isPasswordValid">La contraseña debe tener al menos 8 caracteres.</small>
                                    <small class="text-muted mt-3" x-show="!isCreateMode">Opcional al editar. Solo diligéncialo si deseas cambiarla.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="my-4 text-center" x-show="editMode">
                    <button type="submit" class="btn btn-outline-success btn-lg px-5 fw-bold user-info-save-btn" :disabled="isSubmitDisabled" :class="{'disabled': isSubmitDisabled}">
                        <i class="fa fa-save"></i>&nbsp;
                        <span x-text="isCreateMode ? 'Crear Usuario' : 'Actualizar Informacion'"></span>
                    </button>
                </div>

            </form>

        </div>

    </div>

@endsection
