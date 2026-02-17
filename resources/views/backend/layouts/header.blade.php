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

        @if(Auth::check())
            <div class="session-info">
                <span class="session-name">{{ Auth::user()->username }}</span>
                <span class="session-role">
                    {{
                        Auth::user()->rol == 1 ? 'Soporte' :
                        (Auth::user()->rol == 2 ? 'Administrador' : 'Cajero')
                    }}
                </span>
            </div>
        @endif

        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle user-menu-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Abrir menú de usuario">
                <span class="user-avatar">
                    <i class="fa fa-user"></i>
                </span>
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li class="text-center">    
                    <p class="fw-bold small badge bg-secondary mb-2">
                        {{ 
                            Auth::user()->rol == 1 ? 'Soporte' :
                            (Auth::user()->rol == 2 ? 'Administrador' : 'Cajero') 
                        }}
                    </p>
                </li>

                <li>
                    <a class="dropdown-item text-dark" href="{{ route('users.info', ['id' => Auth::user()->id]) }}">
                        <i class="fa-solid fa-user color-primary"></i>&nbsp;&nbsp;
                        Mi Perfil
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark" href="{{ route('settings.index') }}">
                        <i class="fa-solid fa-gear color-primary"></i>&nbsp;&nbsp;
                        Configuración
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-sign-out-alt"></i>&nbsp;&nbsp;
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
