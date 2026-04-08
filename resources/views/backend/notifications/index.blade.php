@extends('backend.layouts.main')

@section('title', 'Notificaciones')

@push('styles')
    @vite(['resources/css/modules/notifications.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/notifications.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'notifications.inbox',
                    'icon' => 'fas fa-bell',
                    'label' => 'Notificaciones'
                ]
            ])
        @endpush

        <div class="card p-4 section-hero notifications-hero">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                <div class="section-hero-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-0">Centro de Notificaciones</h2>
                    <div class="text-muted small fw-bold">
                        Revisa alertas del sistema y su estado de lectura.
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 section-hero-actions">
                    <span class="status-pill notifications-unread-pill">
                        Sin leer: {{ $unreadCount }}
                    </span>
                    <button type="button" class="btn btn-sm btn-success notifications-mark-all-btn" id="markAllInboxNotificationsBtn" {{ $unreadCount === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-check-double me-1"></i> Marcar todas
                    </button>
                </div>
            </div>
        </div>

        @if($failedJobsCount > 0)
            <div class="card p-3 mt-3 notification-failed-jobs-alert" data-failed-jobs-count="{{ $failedJobsCount }}">
                <div class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center justify-content-between">
                    <div>
                        <div class="fw-bold d-flex align-items-center gap-2">
                            <i class="fas fa-triangle-exclamation"></i>
                            Jobs fallidos en notificaciones: {{ $failedJobsCount }}
                        </div>
                        <div class="small text-muted">
                            @if($lastFailedAt)
                                Último fallo: {{ \Illuminate\Support\Carbon::parse($lastFailedAt)->format('d/m/Y H:i:s') }}
                            @else
                                Revisa la cola para más detalle.
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-warning" id="retryFailedJobsBtn">
                        <i class="fas fa-rotate-right me-1"></i> Reintentar fallidos
                    </button>
                </div>
            </div>
        @endif

        <div class="card p-0 mt-4 section-card">
            <form class="section-toolbar notifications-toolbar" method="GET" action="{{ route('notifications.inbox') }}">
                <select class="form-select form-select-sm section-filter" name="unread">
                    <option value="" {{ $filters['unread'] === null ? 'selected' : '' }}>Todas</option>
                    <option value="1" {{ (string) $filters['unread'] === '1' ? 'selected' : '' }}>Solo sin leer</option>
                </select>

                <select class="form-select form-select-sm section-filter" name="type">
                    <option value="">Todos los tipos</option>
                    @foreach($types as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}" {{ $filters['type'] === $typeKey ? 'selected' : '' }}>
                            {{ $typeLabel }}
                        </option>
                    @endforeach
                </select>

                <select class="form-select form-select-sm section-filter" name="priority">
                    <option value="">Todas las prioridades</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ (string) $filters['priority'] === (string) $i ? 'selected' : '' }}>P{{ $i }}</option>
                    @endfor
                </select>

                <select class="form-select form-select-sm section-filter" name="archived">
                    <option value="active" {{ $filters['archived'] === 'active' ? 'selected' : '' }}>Activas</option>
                    <option value="only" {{ $filters['archived'] === 'only' ? 'selected' : '' }}>Archivadas</option>
                    <option value="all" {{ $filters['archived'] === 'all' ? 'selected' : '' }}>Todas</option>
                </select>

                <select class="form-select form-select-sm section-filter" name="per_page">
                    <option value="10" {{ (string) $filters['per_page'] === '10' ? 'selected' : '' }}>10 por página</option>
                    <option value="15" {{ (string) $filters['per_page'] === '15' ? 'selected' : '' }}>15 por página</option>
                    <option value="25" {{ (string) $filters['per_page'] === '25' ? 'selected' : '' }}>25 por página</option>
                </select>

                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-search me-1"></i> Filtrar
                </button>
            </form>

            <div class="table-responsive">
                <table class="table table-borderless align-middle section-table notifications-table mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Mensaje</th>
                            <th>Prioridad</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($notifications->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center text-muted fw-bold py-4">
                                    No hay notificaciones para mostrar.
                                </td>
                            </tr>
                        @endif

                        @foreach($notifications as $userNotification)
                            @php
                                $notification = $userNotification->notification;
                                $isArchived = (bool) $userNotification->archived_at;
                            @endphp
                            <tr class="{{ $userNotification->read_at ? '' : 'notification-row-unread' }}" data-user-notification-id="{{ $userNotification->id }}">
                                <td>
                                    <button type="button" class="btn btn-icon toggle-notification-detail-btn" title="Ver detalle">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </td>
                                <td>
                                    <span class="table-chip table-chip-type">
                                        {{ \App\Models\Notification::labelForType($notification?->type) }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ $notification?->title ?? '-' }}</td>
                                <td class="text-muted notification-message-preview">{{ $notification?->message ?? '-' }}</td>
                                <td><span class="status-pill status-pill-priority">P{{ $notification?->priority ?? 1 }}</span></td>
                                <td>{{ optional($userNotification->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    @if($isArchived)
                                        <span class="status-pill status-pill-muted">Archivada</span>
                                    @elseif($userNotification->read_at)
                                        <span class="status-pill status-pill-muted">Leída</span>
                                    @else
                                        <span class="status-pill status-pill-success">Sin leer</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if(!$userNotification->read_at)
                                            <button type="button" class="btn btn-icon text-primary mark-one-read-btn" title="Marcar como leída">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        @if(!$isArchived)
                                            <button type="button" class="btn btn-icon archive-one-btn notification-archive-btn" title="Archivar notificación">
                                                <i class="fas fa-box-archive"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="notification-detail-row d-none" data-detail-for="{{ $userNotification->id }}">
                                <td colspan="8">
                                    <div class="notification-detail-card">
                                        <div class="notification-detail-meta">
                                            <div><strong>Título:</strong> {{ $notification?->title ?? '-' }}</div>
                                            <div><strong>Mensaje:</strong> {{ $notification?->message ?? '-' }}</div>
                                            <div><strong>Entrega:</strong> {{ optional($userNotification->delivered_at)->format('d/m/Y H:i:s') ?? '-' }}</div>
                                            <div><strong>Lectura:</strong> {{ optional($userNotification->read_at)->format('d/m/Y H:i:s') ?? 'Pendiente' }}</div>
                                        </div>
                                    </div>
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

@endsection
