<nav class="app-header">

    <div class="header-left">
        <button class="btn header-icon" id="sidebar-toggle" aria-label="Alternar sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="header-breadcrumb">
            @stack('breadcrumb')
        </div>
    </div>

    <div class="header-right">
        <div class="theme-toggle theme-toggle-lg">
            <i class="fas fa-sun"></i>
            <label class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="theme-switch" aria-label="Alternar modo">
            </label>
            <i class="fas fa-moon"></i>
        </div>

        @if(Auth::user()->rol === \App\Models\User::ROLE_SUPPORT)
        {{-- Selector de negocio para soporte --}}
        <div class="nav-item dropdown tenant-switcher-dropdown">
            <button class="btn header-tenant-switch-btn dropdown-toggle" type="button"
                id="tenantSwitcherDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                aria-expanded="false" aria-label="Cambiar negocio">
                <i class="fas fa-building"></i>
                <span class="header-tenant-switch-label">
                    {{ isset($currentTenant) ? $currentTenant->name : 'Sin negocio' }}
                </span>
                @if(isset($currentTenant))
                    <span class="header-tenant-active-dot"></span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow-sm tenant-switcher-menu"
                aria-labelledby="tenantSwitcherDropdown">

                <div class="tenant-switcher-header">
                    <span class="tenant-switcher-title">Cambiar negocio</span>
                    @if(isset($currentTenant))
                        <form action="{{ route('tenants.exit') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-xs tenant-switcher-exit">
                                <i class="fas fa-sign-out-alt me-1"></i> Salir
                            </button>
                        </form>
                    @endif
                </div>

                <div class="tenant-switcher-list">
                    @php
                        $allTenants = \App\Models\Tenant::where('status', true)->orderBy('name')->get();
                    @endphp

                    @forelse($allTenants as $t)
                        <form action="{{ route('tenants.switch', $t->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="tenant-switcher-item {{ isset($currentTenant) && $currentTenant->id === $t->id ? 'tenant-switcher-item--active' : '' }}">
                                <span class="tenant-switcher-item__avatar">
                                    {{ strtoupper(substr($t->name, 0, 1)) }}
                                </span>
                                <span class="tenant-switcher-item__info">
                                    <span class="tenant-switcher-item__name">{{ $t->name }}</span>
                                    <span class="tenant-switcher-item__plan">
                                        {{ [1 => 'Free', 2 => 'Basic', 3 => 'Pro'][$t->plan] ?? '' }}
                                    </span>
                                </span>
                                @if(isset($currentTenant) && $currentTenant->id === $t->id)
                                    <i class="fas fa-check tenant-switcher-item__check"></i>
                                @endif
                            </button>
                        </form>
                    @empty
                        <div class="tenant-switcher-empty">No hay negocios activos.</div>
                    @endforelse
                </div>

                <div class="tenant-switcher-footer">
                    <a href="{{ route('tenants.index') }}" class="tenant-switcher-manage">
                        <i class="fas fa-cog me-1"></i> Gestionar negocios
                    </a>
                </div>

            </div>
        </div>
        @endif

        @if(Auth::user()->tenant_id && Auth::user()->rol !== \App\Models\User::ROLE_SUPPORT)
        {{-- Badge de negocio para usuarios regulares --}}
        <div class="header-tenant-badge" title="{{ isset($userTenant) ? $userTenant->name : '' }}">
            <span class="header-tenant-badge__dot"></span>
            <span class="header-tenant-badge__name">{{ isset($userTenant) ? $userTenant->name : '—' }}</span>
        </div>
        @endif

        <div class="nav-item dropdown notification-dropdown">
            <button class="btn header-notification-btn dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notificaciones">
                <i class="fas fa-bell"></i>
                <span class="header-notification-dot d-none" id="headerNotificationDot"></span>
                <span class="header-notification-count d-none" id="headerNotificationCount">0</span>
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow-sm notification-menu" aria-labelledby="notificationDropdown">
                <div class="notification-menu-header">
                    <div>
                        <div class="notification-menu-title">Notificaciones</div>
                        <div class="notification-menu-subtitle" id="headerNotificationSubtitle">Sin notificaciones nuevas</div>
                    </div>
                    <button class="btn btn-sm btn-link notification-mark-all" type="button" id="markAllNotificationsBtn">
                        Marcar todas
                    </button>
                </div>

                <div class="notification-menu-list" id="headerNotificationList">
                    <div class="notification-empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <span>No hay notificaciones por ahora.</span>
                    </div>
                </div>

                <div class="notification-menu-footer">
                    <a href="{{ route('notifications.inbox') }}" class="notification-view-all-link">
                        Ver todas
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-item dropdown header-user-menu">
            <a class="nav-link dropdown-toggle user-menu-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Abrir menú de usuario">
                <span class="user-avatar">
                    <i class="fa fa-user"></i>
                </span>
                @if(Auth::check())
                    <span class="session-info">
                        <span class="session-name">{{ Auth::user()->username }}</span>
                        <span class="session-role">
                            {{
                                Auth::user()->rol == \App\Models\User::ROLE_ROOT ? 'Root' :
                                (Auth::user()->rol == \App\Models\User::ROLE_ADMIN ? 'Administrador' :
                                (Auth::user()->rol == \App\Models\User::ROLE_SALES ? 'Cajero' : 'Soporte'))
                            }}
                        </span>
                    </span>
                @endif
                <span class="user-menu-caret">
                    <i class="fa-solid fa-chevron-down"></i>
                </span>
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end shadow-sm user-menu-dropdown">
                <li class="user-menu-dropdown-header text-center">    
                    <p class="fw-bold small badge user-role-badge mb-2">
                        {{ 
                            Auth::user()->rol == 1 ? 'Soporte' :
                            (Auth::user()->rol == 2 ? 'Administrador' : 'Cajero') 
                        }}
                    </p>
                </li>

                <li>
                    <a class="dropdown-item user-menu-item" href="{{ route('users.info', ['id' => Auth::user()->id]) }}">
                        <span class="user-menu-item-icon">
                            <i class="fa-solid fa-user color-primary"></i>
                        </span>
                        Mi Perfil
                    </a>
                </li>

                <li>
                    <a class="dropdown-item user-menu-item" href="{{ route('settings.index') }}">
                        <span class="user-menu-item-icon">
                            <i class="fa-solid fa-gear color-primary"></i>
                        </span>
                        Configuración
                    </a>
                </li>

                <li>
                    <a class="dropdown-item user-menu-item" href="{{ route('notifications.inbox') }}">
                        <span class="user-menu-item-icon">
                            <i class="fa-solid fa-bell color-primary"></i>
                        </span>
                        Mis Notificaciones
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item user-menu-item user-menu-item-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <span class="user-menu-item-icon">
                            <i class="fa-solid fa-sign-out-alt"></i>
                        </span>
                        Cerrar sesión
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none hover-danger">
                        @csrf
                    </form>

                </li>

            </ul>
        </div>
    </div>

</nav>
