@extends('backend.layouts.guest')

@section('title', 'Login')

@push('scripts')
    @vite(['resources/js/modules/auth.js'])
@endpush

@section('content')

    <div class="d-flex justify-content-center align-items-center vh-100">

        <div class="p-5 rounded shadow login-form bg-white border border-light" style="max-width: 400px; width: 100%;">

            <span class="d-flex justify-content-center mb-3">
                <img src="{{ asset('images/ll.png') }}" class="sidebar-logo me-3">
            </span>
            
            <p class="text-center text-muted my-3 fw-bold">Inicia sesión para continuar</p>

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="username" class="form-label fw-bold">
                        <i class="fas fa-user fa-lg color-primary"></i>&nbsp;&nbsp;
                        Usuario
                    </label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required aria-label="Usuario">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">
                        <i class="fas fa-lock fa-lg color-primary"></i>&nbsp;&nbsp;
                        Contraseña
                    </label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required aria-label="Contraseña">
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100 my-3 fw-bold shadow">
                    Iniciar sesión
                </button>
            </form>

            @if ($errors->any())
                <div class="alert alert-danger">
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
