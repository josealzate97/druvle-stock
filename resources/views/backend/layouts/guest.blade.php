<!DOCTYPE html>

<html lang="es">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">

        <title>@yield('title', 'Druvle')</title>

        <!-- Font Awesome 6.5.2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- Bootstrap 5.3.3 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">

        <!-- Notyf -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('scripts')
    
    </head>

    <body style="min-height:100vh; @if(request()->is('login')) background: url('{{ asset('images/login_bg.png') }}') no-repeat center center fixed; background-size: cover; @endif">
        @yield('content')
    </body>
    
</html>