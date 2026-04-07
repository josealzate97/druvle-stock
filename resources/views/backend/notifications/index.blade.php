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
                    <button type="button" class="btn btn-sm btn-primary" id="markAllInboxNotificationsBtn" {{ $unreadCount === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-check-double me-1"></i> Marcar todas
                    </button>
                </div>
            </div>
        </div>

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
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Mensaje</th>
                            <th>Prioridad</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($notifications->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted fw-bold py-4">
                                    No hay notificaciones para mostrar.
                                </td>
                            </tr>
                        @endif

                        @foreach($notifications as $userNotification)
                            @php
                                $notification = $userNotification->notification;
                            @endphp
                            <tr class="{{ $userNotification->read_at ? '' : 'notification-row-unread' }}" data-user-notification-id="{{ $userNotification->id }}">
                                <td>
                                    <span class="table-chip table-chip-type">
                                        {{ \App\Models\Notification::labelForType($notification?->type) }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ $notification?->title ?? '-' }}</td>
                                <td class="text-muted">{{ $notification?->message ?? '-' }}</td>
                                <td><span class="status-pill status-pill-priority">P{{ $notification?->priority ?? 1 }}</span></td>
                                <td>{{ optional($userNotification->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    @if($userNotification->read_at)
                                        <span class="status-pill status-pill-muted">Leída</span>
                                    @else
                                        <span class="status-pill status-pill-success">Sin leer</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(!$userNotification->read_at)
                                        <button type="button" class="btn btn-icon text-primary mark-one-read-btn" title="Marcar como leída">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
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

