
@push('scripts')
    @vite(['resources/css/app.css'])
@endpush

<div class="sidebar">

    <a href="{{ route('home') }}" class="sidebar-brand d-flex">
        <span class="fs-1 ml-4 mt-3">Druvle</span>
    </a>

    <ul class="sidebar-nav">

        <li class="sidebar-item">
            <a href="{{ route('home') }}" class="sidebar-link" url="home">
                <i class="fa-solid fa-th color-primary"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('categories.index') }}" class="sidebar-link" url="categories">
                <i class="fa-solid fa-tags color-primary"></i>
                <span>Categorías</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('products.index') }}" class="sidebar-link" url="products">
                <i class="fa-solid fa-box color-primary"></i>
                <span>Productos</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="{{ route('sales.index') }}" class="sidebar-link" url="sales">
                <i class="fa-solid fa-shopping-cart color-primary"></i>
                <span>Ventas</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('reports.index') }}" class="sidebar-link" url="reports">
                <i class="fa-solid fa-chart-line color-primary"></i>
                <span>Reportes</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ route('users.index') }}" class="sidebar-link" url="users">
                <i class="fa-solid fa-users color-primary"></i>
                <span>Usuarios</span>
            </a>
        </li>

    </ul>

    <ul class="sidebar-nav sidebar-bottom">

        <li class="sidebar-item">
            <a href="{{ route('settings.index') }}" class="sidebar-link" url="settings">
                <i class="fa-solid fa-cog color-primary"></i>
                <span>Configuraciones</span>
            </a>
        </li>

        <li class="sidebar-item">

            <a href="{{ route('logout') }}" class="sidebar-link text-danger"
                onclick="event.preventDefault();
                document.getElementById('logout-form-sidebar').submit();">
                <i class="fa-solid fa-sign-out-alt"></i>
                <span>Cerrar Sesion</span>
            </a>

            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

        </li>

    </ul>

</div>