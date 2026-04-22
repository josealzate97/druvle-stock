
<div class="sidebar">

    <a href="{{ route('home') }}" class="sidebar-brand d-flex">
        <span class="sidebar-title">Druvle</span>
    </a>

    @if(Auth::user()->rol === \App\Models\User::ROLE_SUPPORT && isset($currentTenant))
        <div class="sidebar-tenant-context">
            <div class="sidebar-tenant-context__badge">
                <span class="sidebar-tenant-context__dot"></span>
                Modo negocio
            </div>
            <div class="sidebar-tenant-context__name">
                <i class="fas fa-building"></i>
                <span>{{ $currentTenant->name }}</span>
            </div>
            <form action="{{ route('tenants.exit') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="sidebar-tenant-context__exit">
                    <i class="fas fa-arrow-left"></i>
                    Salir del negocio
                </button>
            </form>
        </div>
    @endif

    <ul class="sidebar-nav">

        <li class="sidebar-item">
            <a href="{{ route('home') }}" class="sidebar-link" url="home">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-th"></i>
                </span>
                <span>Dashboard</span>
            </a>
        </li>

        @if(Auth::user()->rol === \App\Models\User::ROLE_SUPPORT && !isset($currentTenant))

            <li class="sidebar-item">
                <a href="{{ route('tenants.index') }}" class="sidebar-link" url="tenants">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-building"></i>
                    </span>
                    <span>Negocios</span>
                </a>
            </li>

        @else

            <li class="sidebar-item">
                <a href="{{ route('categories.index') }}" class="sidebar-link" url="categories">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-tags"></i>
                    </span>
                    <span>Categorías</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('products.index') }}" class="sidebar-link" url="products">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-box"></i>
                    </span>
                    <span>Productos</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="{{ route('sales.index') }}" class="sidebar-link" url="sales">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-shopping-cart"></i>
                    </span>
                    <span>Ventas</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('reports.index') }}" class="sidebar-link" url="reports">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    <span>Reportes</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('users.index') }}" class="sidebar-link" url="users">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-users"></i>
                    </span>
                    <span>Usuarios</span>
                </a>
            </li>

        @endif

    </ul>

    <ul class="sidebar-nav sidebar-bottom">

        @if(Auth::user()->rol !== \App\Models\User::ROLE_SUPPORT || isset($currentTenant))
            <li class="sidebar-item">
                <a href="{{ route('settings.index') }}" class="sidebar-link" url="settings">
                    <span class="sidebar-icon">
                        <i class="fa-solid fa-cog"></i>
                    </span>
                    <span>Configuraciones</span>
                </a>
            </li>
        @endif

        <li class="sidebar-item">

            <a href="{{ route('logout') }}" class="sidebar-link sidebar-link--logout"
                onclick="event.preventDefault();
                document.getElementById('logout-form-sidebar').submit();">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-sign-out-alt"></i>
                </span>
                <span>Cerrar Sesion</span>
            </a>

            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

        </li>

    </ul>

</div>
