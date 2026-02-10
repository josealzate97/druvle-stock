@extends('backend.layouts.main')

@section('title', 'Configuración')

@push('scripts')
    @vite(['resources/js/modules/settings.js'])
@endpush

@section('content')

    <div class="container-fluid p-4">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'settings.index',
                    'icon' => 'fas fa-cogs',
                    'label' => 'Configuraciones'
                ]
            ])
        @endpush

        <ul class="nav nav-pills rounded p-3 gap-1 bg-white mb-4 border" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">Información Básica</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="taxes-tab" data-bs-toggle="tab" data-bs-target="#taxes" type="button" role="tab">Impuestos</button>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabsContent">

            <div class="card p-4 tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab" x-data="settingsForm({
                id: '{{ $settings->id }}',
                company_name: '{{ $settings->company_name }}',
                nit: '{{ $settings->nit }}',
                phone: '{{ $settings->phone }}',
                address: '{{ $settings->address }}',
            })">

        
                <div class="col-lg-12 d-flex flex-wrap justify-content-between align-items-center">

                    <div class="col-lg-8 col-md-6 col-sm-12">

                        <h3 class="fw-bold">
                            <i class="fas fa-cogs me-2 color-primary"></i>
                            Configuración de la Empresa
                        </h3>
                        <span class="text-muted small fw-bold">Ajustes generales de la empresa</span>

                    </div>
                        
                    <div class="col-lg-4 col-md-6 col-sm-12 text-end">
                        <!-- Botón para habilitar/deshabilitar el formulario -->
                        <button class="btn btn-warning mb-3" @click="toggleEdit">
                            <i class="fas fa-edit"></i> <span x-text="editMode ? 'Cancelar' : 'Editar'"></span>
                        </button>
                    </div>

                </div>

                <hr>

                <!-- Formulario -->
                <form @submit.prevent="saveSettings">

                    <div class="row g-4">

                        <!-- ID oculto -->
                        <input type="hidden" name="id" x-model="form.id">

                        <!-- Nombre de la empresa -->
                        <div class="col-lg-4 col-md-12 col-sm-12">
                            <label for="company_name" class="form-label fw-bold">Razón social</label>
                            <input type="text" id="company_name" name="company_name" class="form-control" x-model="form.company_name" :disabled="!editMode">
                        </div>

                        <!-- NIT -->
                        <div class="col-lg-4 col-md-12 col-sm-12">
                            <label for="nit" class="form-label fw-bold">NIF / CIF</label>
                            <input type="text" id="nit" name="nit" class="form-control" x-model="form.nit" :disabled="!editMode">
                        </div>

                        <!-- Teléfono -->
                        <div class="col-lg-4 col-md-12 col-sm-12">
                            <label for="phone" class="form-label fw-bold">Teléfono</label>
                            <input type="text" id="phone" name="phone" class="form-control" x-model="form.phone" :disabled="!editMode">
                        </div>

                        <!-- Dirección -->
                        <div class="col-lg-4 col-md-12 col-sm-12">
                            <label for="address" class="form-label fw-bold">Dirección Fiscal</label>
                            <input type="text" id="address" name="address" class="form-control" x-model="form.address" :disabled="!editMode">
                        </div>

                    </div>

                    <!-- Botón para guardar -->
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-success col-4 btn-lg" :disabled="!editMode">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>

                </form>

            </div>

            <div class="tab-pane fade card p-4" id="taxes" role="tabpanel" aria-labelledby="taxes-tab">
                
                <div class="col-lg-12 col-md-6 col-sm-12 d-flex flex-wrap justify-content-between align-items-center mb-4">

                    <div class="col-6">
                        <h3 class="fw-bold">
                            <i class="fas fa-percent me-2 color-primary"></i>
                            Configuración de impuestos
                        </h3>
                        <span class="text-muted fw-bold small">Listado de impuestos del sistema</span>
                    </div>

                    <div class="col-6">
                        <button class="btn btn-success float-end" id="btnNewTax" type="button">
                            <i class="fas fa-plus me-1"></i> Crear impuesto
                        </button>
                    </div>

                </div>

                <hr>

                @include('backend.taxes.index', ['taxes' => $taxes])

            </div>
        
        </div>

    </div>

@endsection
