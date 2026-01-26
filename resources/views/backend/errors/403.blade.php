
@extends('backend.layouts.main')

@section('title', 'Acceso Denegado')

@section('content')

    <div class="container-fluid p-4">

        <div class="card p-4 text-center">

            <h1 class="fw-bold text-danger">403</h1>
            <h4 class="fw-bold">Acceso Denegado</h4>
            <p class="text-muted">No tienes permisos para acceder a esta página.</p>
            
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                <i class="fa fa-home"></i> Volver al Inicio
            </a>

        </div>

    </div>

@endsection