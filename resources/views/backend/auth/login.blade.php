@extends('backend.layouts.guest')

@section('title', 'Login')

@push('scripts')
    @vite(['resources/js/modules/auth.js'])
@endpush

@section('content')

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-card__brand">
                <img src="{{ asset('images/ll.png') }}" alt="Druvle" class="login-logo">
                <div>
                    <h1>Druvle</h1>
                    <p>Accede a tu panel de ventas</p>
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
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

@endsection
