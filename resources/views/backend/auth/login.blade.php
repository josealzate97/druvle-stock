@extends('backend.layouts.guest')

@section('title', 'Login')

@push('scripts')
    @vite(['resources/js/modules/auth.js'])
@endpush

@section('content')

    <div class="login-split">

        <div class="login-panel login-panel--brand">

            <div class="brand-content">

                <div class="login-hero-icon">
                    <i class="fas fa-right-to-bracket"></i>
                </div>

                <h1>Druvle</h1>
                <p>Gestiona inventario, ventas y reportes con claridad.</p>

                <div class="brand-stats">
                    <div>
                        <span>Panel</span>
                        <strong>Inteligente</strong>
                    </div>
                    <div>
                        <span>Datos</span>
                        <strong>En tiempo real</strong>
                    </div>
                </div>

            </div>

        </div>

        <div class="login-panel login-panel--form">

            <div class="login-card">

                <div class="login-card__brand">
                    <div class="login-hero-icon login-hero-icon--sm">
                        <i class="fas fa-right-to-bracket"></i>
                    </div>
                    <div>
                        <h2>Bienvenido</h2>
                        <p>Inicia sesión para continuar</p>
                    </div>
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}" class="login-form">
                    
                    @csrf

                    <div class="login-field">
                        <label for="username" class="form-label">Usuario</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required aria-label="Usuario">
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required aria-label="Contraseña">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                        Iniciar sesión
                    </button>
                </form>

            @if ($errors->any())

                <div class="login-alert">
                    <i class="fas fa-circle-exclamation"></i>
                    <div>
                        <strong>Ups, algo salió mal</strong>
                        <span>Usuario o contraseña incorrectos.</span>
                    </div>
                </div>

            @endif
        </div>

    </div>

@endsection
