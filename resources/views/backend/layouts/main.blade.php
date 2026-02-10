<!DOCTYPE html>
<html lang="es">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Druvle')</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">

        <!-- CSS - Font Awesome 6.5.2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- CSS - Bootstrap 5.3.3 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">

        <!-- CSS - Notyf -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/sidebar.css'])
        @stack('scripts')

        <!-- JS - Bootstrap 5.3.3 -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="" crossorigin="anonymous"></script>

        <!-- JS - Notyf -->
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

        <!-- JS - Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- JS - IMask -->
        <script src="https://unpkg.com/imask"></script>

    </head>

    <body>
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
