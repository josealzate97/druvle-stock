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

            <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab" x-data="settingsForm({
                id: '{{ $settings->id }}',
                company_name: '{{ $settings->company_name }}',
                nit: '{{ $settings->nit }}',
                phone: '{{ $settings->phone }}',
                address: '{{ $settings->address }}',
            })">
                <div class="card p-4 section-hero settings-hero">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                        <div class="section-hero-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="fw-bold mb-0">Configuración de la Empresa</h2>
                            <div class="text-muted small fw-bold">Ajustes generales y datos fiscales de tu negocio.</div>
                        </div>
                        <button class="btn btn-warning settings-edit-btn" @click="toggleEdit">
                            <i class="fas fa-edit"></i> <span x-text="editMode ? 'Cancelar' : 'Editar'"></span>
                        </button>
                    </div>
                </div>

                <div class="card p-4 mt-4 section-card settings-form-card">
                    <form @submit.prevent="saveSettings">

                        <div class="row g-4">
                            <input type="hidden" name="id" x-model="form.id">

                            <div class="col-12">
                                <div class="settings-section-title">Información general</div>
                            </div>

                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <label for="company_name" class="form-label fw-bold">Razón social</label>
                                <input type="text" id="company_name" name="company_name" class="form-control" x-model="form.company_name" :disabled="!editMode">
                            </div>

                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <label for="nit" class="form-label fw-bold">NIF / CIF</label>
                                <input type="text" id="nit" name="nit" class="form-control" x-model="form.nit" :disabled="!editMode">
                            </div>

                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <label for="phone" class="form-label fw-bold">Teléfono</label>
                                <input type="text" id="phone" name="phone" class="form-control" x-model="form.phone" :disabled="!editMode">
                            </div>

                            <div class="col-12">
                                <div class="settings-section-title">Dirección fiscal</div>
                            </div>

                            <div class="col-lg-8 col-md-12 col-sm-12">
                                <label for="address" class="form-label fw-bold">Dirección</label>
                                <input type="text" id="address" name="address" class="form-control" x-model="form.address" :disabled="!editMode">
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success btn-lg px-5" :disabled="!editMode">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            <div class="tab-pane fade" id="taxes" role="tabpanel" aria-labelledby="taxes-tab">
                
                <div class="card p-4 section-hero settings-hero">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                        <div class="section-hero-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="fw-bold mb-0">Configuración de Impuestos</h2>
                            <div class="text-muted fw-bold small">Listado de impuestos del sistema.</div>
                        </div>
                        <button class="btn btn-success" id="btnNewTax" type="button">
                            <i class="fas fa-plus me-1"></i> Crear impuesto
                        </button>
                    </div>
                </div>

                <div class="card p-4 mt-4 section-card">
                    @include('backend.taxes.index', ['taxes' => $taxes])
                </div>

            </div>
        
        </div>

    </div>

@endsection
