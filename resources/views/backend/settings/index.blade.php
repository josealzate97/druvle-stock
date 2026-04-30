@extends('backend.layouts.main')

@section('title', 'Configuración')

@push('styles')
    @vite(['resources/css/modules/settings.css', 'resources/css/modules/modals.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/settings.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'settings.index',
                    'icon' => 'fas fa-cogs',
                    'label' => 'Configuraciones'
                ]
            ])
        @endpush

        <div class="card p-2 mb-0 module-tabs-bar module-tabs-connected">
            <ul class="nav nav-pills module-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-3" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                        <i class="fas fa-building me-1"></i> Información General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3" id="taxes-tab" data-bs-toggle="tab" data-bs-target="#taxes" type="button" role="tab">
                        <i class="fas fa-percent me-1"></i> Impuestos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                        <i class="fas fa-bell me-1"></i> Notificaciones
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content settings-content module-tabs-content" id="settingsTabsContent">

            <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab" x-data="settingsForm({
                id: '{{ $settings->id }}',
                company_name: '{{ $settings->company_name }}',
                nit: '{{ $settings->nit }}',
                phone: '{{ $settings->phone }}',
                address: '{{ $settings->address }}',
                city: '{{ $settings->city ?? '' }}',
                logo: '{{ $settings->logo ?? '' }}',
            })">
                <div class="card p-4 section-hero settings-hero border-0 shadow-sm">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                        <div class="section-hero-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="fw-bold mb-0">Configuración de la Empresa</h2>
                            <div class="text-muted small">Ajustes generales y datos fiscales de tu negocio.</div>
                        </div>
                        <button class="btn settings-edit-btn edit-solid-btn" @click="toggleEdit">
                            <i class="fas fa-edit"></i> <span x-text="editMode ? 'Cancelar' : 'Editar'"></span>
                        </button>
                    </div>
                </div>

                <div class="card p-4 mt-4 section-card settings-form-card shadow-sm">
                    <form @submit.prevent="saveSettings">

                        <input type="hidden" name="id" x-model="form.id">

                        <div class="d-flex gap-4 settings-main-layout">

                            <!-- Columna izquierda: datos de empresa + dirección -->
                            <div class="flex-grow-1">

                                <div class="row g-4">

                                    <div class="col-12">
                                        <div class="settings-section-title">
                                            <i class="fas fa-info-circle me-1"></i> Información general
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                        <label for="company_name" class="form-label fw-bold">Razón social</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control" x-model="form.company_name" :disabled="!editMode">
                                    </div>

                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                        <label for="nit" class="form-label fw-bold">NIF / CIF</label>
                                        <input type="text" id="nit" name="nit" class="form-control" x-model="form.nit" :disabled="!editMode">
                                    </div>

                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                        <label for="phone" class="form-label fw-bold">Teléfono</label>
                                        <input type="text" id="phone" name="phone" class="form-control" x-model="form.phone" :disabled="!editMode">
                                    </div>

                                    <div class="col-12">
                                        <div class="settings-section-title">
                                            <i class="fas fa-map-marker-alt me-1"></i> Dirección fiscal
                                        </div>
                                    </div>

                                    <div class="col-lg-8 col-md-12 col-sm-12">
                                        <label for="address" class="form-label fw-bold">Dirección</label>
                                        <input type="text" id="address" name="address" class="form-control" x-model="form.address" :disabled="!editMode">
                                    </div>

                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                        <label for="city" class="form-label fw-bold">Ciudad</label>
                                        <input type="text" id="city" name="city" class="form-control" x-model="form.city" :disabled="!editMode" placeholder="Ej. Madrid">
                                    </div>

                                </div>

                            </div>

                            <!-- Columna derecha: logo -->
                            <div class="settings-logo-col">

                                <div class="settings-section-title mb-3">
                                    <i class="fas fa-image me-1"></i> Logo de la empresa
                                </div>

                                <div class="settings-logo-preview">
                                    <template x-if="form.logo">
                                        <img :src="form.logo" alt="Logo empresa" class="settings-logo-img">
                                    </template>
                                    <template x-if="!form.logo">
                                        <span class="settings-logo-placeholder">
                                            <i class="fas fa-image"></i>
                                        </span>
                                    </template>
                                </div>

                                <div class="mt-3">
                                    <label for="logo" class="form-label fw-bold">URL del logo</label>
                                    <input type="text" id="logo" name="logo" class="form-control" x-model="form.logo" :disabled="!editMode" placeholder="https://...">
                                    <div class="form-text">Introduce la URL pública de la imagen.</div>
                                </div>

                            </div>

                        </div>

                        <div class="mt-4 d-flex justify-content-center my-4">
                            <button type="submit" class="btn btn-outline-success btn-lg col-4 px-4" :disabled="!editMode">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            <div class="tab-pane fade" id="taxes" role="tabpanel" aria-labelledby="taxes-tab">
                
                <div class="card p-4 section-hero settings-hero border-0 shadow-sm">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                        <div class="section-hero-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="fw-bold mb-0">Configuración de Impuestos</h2>
                            <div class="text-muted small">Listado de impuestos del sistema.</div>
                        </div>
                        <button class="btn btn-success" id="btnNewTax" type="button">
                            <i class="fas fa-plus me-1"></i> Crear impuesto
                        </button>
                    </div>
                </div>

                <div class="card p-0 mt-4 section-card shadow-sm">
                    @include('backend.taxes.index', ['taxes' => $taxes])
                </div>

            </div>

            <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">

                <div class="card p-4 section-hero settings-hero border-0 shadow-sm">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                        <div class="section-hero-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="fw-bold mb-0">Configuración de Notificaciones</h2>
                            <div class="text-muted small">Administra notificaciones internas del sistema.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 section-hero-actions">
                            <button class="btn btn-success btn-sm" id="btnNewNotification" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal" data-bs-mode="new">
                                <i class="fas fa-plus me-1"></i> Crear notificación
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card p-0 mt-4 section-card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle section-table notification-table mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Título</th>
                                    <th>Prioridad</th>
                                    <th>Programada</th>
                                    <th>Expira</th>
                                    <th>Creada</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($notifications->isEmpty())
                                    <tr>
                                        <td colspan="7">
                                            <div class="sd-empty-state">
                                                <span class="sd-empty-icon">
                                                    <i class="fas fa-bell-slash"></i>
                                                </span>
                                                <p class="sd-empty-title">Sin notificaciones configuradas</p>
                                                <p class="sd-empty-desc">Crea una notificación para mantener informado al equipo sobre eventos del sistema.</p>
                                                <button class="btn btn-sm btn-success px-4" data-bs-toggle="modal" data-bs-target="#notificationModal" data-bs-mode="new">
                                                    <i class="fas fa-plus me-1"></i> Crear notificación
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($notifications as $notification)
                                    <tr>
                                        <td><span class="table-chip table-chip-type">{{ \App\Models\Notification::labelForType($notification->type) }}</span></td>
                                        <td>
                                            <div class="fw-bold">{{ $notification->title }}</div>
                                            <div class="small text-muted text-truncate notification-message-preview">{{ $notification->message }}</div>
                                        </td>
                                        <td><span class="status-pill status-pill-priority">P{{ $notification->priority }}</span></td>
                                        <td>{{ optional($notification->scheduled_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td>{{ optional($notification->expires_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td>{{ optional($notification->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-icon text-primary" title="Editar" onclick='editNotification(@json($notification))'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon text-danger" title="Eliminar" onclick="deleteNotification('{{ $notification->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="section-footer">
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        
        </div>

    </div>

    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <form id="notificationForm">
            <div class="modal-dialog modal-lg">
                <div class="modal-content form-modal">
                    <div class="modal-header">
                        <div class="modal-title-block">
                            <h4 class="modal-title" id="notificationModalLabel">
                                <span class="modal-icon">
                                    <i class="fas fa-bell"></i>
                                </span>
                                Crear notificación
                            </h4>
                            <span class="modal-subtitle">Configura título, mensaje y programación.</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <input type="hidden" id="notificationId" name="id">

                        <div class="col-md-6">
                            <label for="notificationType" class="form-label fw-bold">Tipo *</label>
                            <select class="form-select" id="notificationType" name="type" required>
                                <option value="{{ \App\Models\Notification::TYPE_STOCK_LOW }}">Stock Bajo</option>
                                <option value="{{ \App\Models\Notification::TYPE_REFUND }}">Devoluciones</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="notificationPriority" class="form-label fw-bold">Prioridad (1-5)</label>
                            <input type="number" min="1" max="5" class="form-control" id="notificationPriority" name="priority" value="1">
                        </div>

                        <div class="col-12">
                            <div class="notification-target-box">
                                <div class="notification-target-title">
                                    <i class="fas fa-users me-1"></i> Destino de la notificación
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-4">
                                        <label for="notificationTargetType" class="form-label fw-bold">Enviar a</label>
                                        <select class="form-select" id="notificationTargetType" name="target_type">
                                            <option value="all_active">Todos los usuarios activos</option>
                                            <option value="role">Usuarios por rol</option>
                                            <option value="users">Usuarios específicos</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 d-none" id="notificationTargetRoleWrapper">
                                        <label for="notificationTargetRole" class="form-label fw-bold">Rol</label>
                                        <select class="form-select" id="notificationTargetRole" name="target_role">
                                            <option value="">Selecciona rol</option>
                                            <option value="{{ \App\Models\User::ROLE_ROOT }}">Super Admin</option>
                                            <option value="{{ \App\Models\User::ROLE_ADMIN }}">Admin</option>
                                            <option value="{{ \App\Models\User::ROLE_SALES }}">Cajero</option>
                                        </select>
                                    </div>

                                    <div class="col-md-8 d-none" id="notificationTargetUsersWrapper">
                                        <label for="notificationTargetUsers" class="form-label fw-bold">Usuarios</label>
                                        <select class="form-select" id="notificationTargetUsers" name="user_ids[]" multiple size="5">
                                            @foreach ($activeUsers as $activeUser)
                                                <option value="{{ $activeUser->id }}">
                                                    {{ $activeUser->name }} {{ $activeUser->lastname }} ({{ $activeUser->username }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Mantén presionado Ctrl o Cmd para seleccionar varios.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="notificationTitle" class="form-label fw-bold">Título *</label>
                            <input type="text" class="form-control" id="notificationTitle" name="title" maxlength="150" required>
                        </div>

                        <div class="col-12">
                            <label for="notificationMessage" class="form-label fw-bold">Mensaje *</label>
                            <textarea class="form-control" id="notificationMessage" name="message" rows="3" maxlength="1000" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="notificationScheduledAt" class="form-label fw-bold">Programada</label>
                            <input type="datetime-local" class="form-control" id="notificationScheduledAt" name="scheduled_at">
                        </div>

                        <div class="col-md-6">
                            <label for="notificationExpiresAt" class="form-label fw-bold">Expira</label>
                            <input type="datetime-local" class="form-control" id="notificationExpiresAt" name="expires_at">
                        </div>

                    </div>

                    <div class="modal-footer col-12 d-flex justify-content-between gap-2 my-3">
                        <button type="button" class="btn btn-outline-danger col-5" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success col-5" id="saveNotificationBtn">
                            <i class="fas fa-save me-2"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
