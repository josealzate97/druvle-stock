<!DOCTYPE html>

<html lang="es">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="@yield('meta_description', 'Druvle: acceso seguro al panel de inventario, ventas y reportes.')">

        <link rel="icon" type="image/x-icon" href="{{ asset('images/icon.png') }}">

        <title>@yield('title', 'Druvle')</title>

        <!-- Vite Assets -->
        @vite(['resources/css/guest.css', 'resources/css/auth.css'])

        @stack('scripts')
    
    </head>

    <body>
        <main role="main">
            @yield('content')
        </main>
    </body>
    
</html>
