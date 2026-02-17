<!DOCTYPE html>
<html lang="es">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="@yield('meta_description', 'Druvle: panel de gestión de inventario, ventas y reportes en tiempo real.')">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Druvle')</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/icon.png') }}">

        <!-- Vite Assets -->
        @vite(['resources/css/vendor.css', 'resources/css/app.css', 'resources/js/app.js', 'resources/css/sidebar.css'])
        @stack('styles')
        @stack('scripts')

    </head>

    <body>

        <script>

            (() => {
                try {
                    const savedTheme = localStorage.getItem('theme-mode');
                    if (savedTheme === 'dark') {
                        document.body.classList.add('theme-dark');
                    }
                } catch (e) {}
            })();

        </script>

        <div id="loading-overlay" class="page-loader-overlay" aria-live="polite">

            <div class="page-loader">
                <div class="page-loader__orbit">
                    <span></span><span></span><span></span>
                </div>
                <div class="page-loader__text">
                    <strong>Preparando tu panel</strong>
                    <span>Cargando recursos...</span>
                </div>
            </div>
            
        </div>

        <div class="wrapper d-flex">

            <aside id="sidebar">    
                @include('backend.layouts.sidebar')
            </aside>

            <div class="main-content flex-grow-1 d-flex flex-column">

                <header>
                    @include('backend.layouts.header')
                </header>

                <main role="main" class="flex-grow-1">
                    @yield('content')
                </main>

            </div>
            
        </div>

    </body>
    
</html>
