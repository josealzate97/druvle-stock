<nav class="navbar navbar-expand-lg navbar-light px-4">

    <button class="btn" id="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="navbar-nav ms-auto">

        <li class="nav-item dropdown">

            <a class="nav-link dropdown-toggle fw-bold text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                
                <span class="user-icon-container">
                    <i class="fa fa-user color-primary"></i>
                </span>

                @if(Auth::check())
                    <span class="color-primary">{{ Auth::user()->username }}</span>
                @endif

            </a>
            
            <ul class="dropdown-menu dropdown-menu-end">
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

        </li>

    </ul>

</nav>
