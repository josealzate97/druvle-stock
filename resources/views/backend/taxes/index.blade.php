
@section('title', 'Impuestos')

@push('styles')
    @vite(['resources/css/modules/taxes.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/taxes.js'])
@endpush

<div class="section-toolbar">
    <div class="section-search">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control form-control-sm" placeholder="Buscar impuesto...">
    </div>
    <select class="form-select form-select-sm section-filter">
        <option value="">Todos los estados</option>
        <option value="active">Activo</option>
        <option value="inactive">Inactivo</option>
    </select>
</div>

{{-- Tabla desktop (lg+) --}}
<div class="d-none d-lg-block">
    <div class="table-responsive">

        <table class="table table-borderless align-middle section-table taxes-table">

            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Porcentaje</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @if ($taxes->isEmpty())
                    <tr>
                        <td colspan="4">
                            <div class="sd-empty-state">
                                <span class="sd-empty-icon">
                                    <i class="fas fa-percent"></i>
                                </span>
                                <p class="sd-empty-title">Sin impuestos registrados</p>
                                <p class="sd-empty-desc">Crea un impuesto para poder asignarlo a tus productos.</p>
                                <button class="btn btn-sm btn-success px-4" id="btnNewTaxEmpty" type="button">
                                    <i class="fas fa-plus me-1"></i> Crear impuesto
                                </button>
                            </div>
                        </td>
                    </tr>
                @endif
                
                @foreach($taxes as $tax)

                    <tr>

                        <td>
                            <div class="fw-bold">{{ $tax->name }}</div>
                        </td>
                        <td class="text-muted">{{ number_format($tax->rate, 2) }} %</td>

                        <td>
                            @if($tax->status)
                                <span class="status-pill status-pill-success">Activo</span>
                            @else
                                <span class="status-pill status-pill-muted">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-end">

                            <button class="btn btn-icon text-primary" onclick='editTax(@json($tax))' data-bs-toggle="modal" data-bs-target="#taxModal" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>

                            @if($tax->status)

                                <button class="btn btn-icon text-danger" onclick="deleteTax('{{ $tax->id }}')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>

                            @else

                                <button class="btn btn-icon text-success" onclick="activateTax('{{ $tax->id }}')" title="Activar">
                                    <i class="fas fa-check"></i>
                                </button>

                            @endif
                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>
</div>

{{-- Card Slider (móvil / tablet < lg) --}}
<div class="d-lg-none tax-slider-wrapper">

    @if($taxes->isEmpty())
        <div class="p-4">
            <div class="sd-empty-state">
                <span class="sd-empty-icon">
                    <i class="fas fa-percent"></i>
                </span>
                <p class="sd-empty-title">Sin impuestos registrados</p>
                <p class="sd-empty-desc">Crea un impuesto para poder asignarlo a tus productos.</p>
                <button class="btn btn-sm btn-success px-4" id="btnNewTaxEmpty" type="button">
                    <i class="fas fa-plus me-1"></i> Crear impuesto
                </button>
            </div>
        </div>
    @else

        <div class="tax-slider" id="taxSlider">
            @foreach($taxes as $tax)
            <div class="tax-slide" data-id="{{ $tax->id }}">
                <div class="tax-card">

                    <div class="tax-card-header">
                        <div class="fw-bold text-truncate">{{ $tax->name }}</div>
                        @if($tax->status)
                            <span class="status-pill status-pill-success ms-auto flex-shrink-0">Activo</span>
                        @else
                            <span class="status-pill status-pill-muted ms-auto flex-shrink-0">Inactivo</span>
                        @endif
                    </div>

                    <div class="tax-card-rate">
                        <span class="text-muted small fw-bold">Porcentaje</span>
                        <span class="fw-bold tax-card-rate-value">{{ number_format($tax->rate, 2) }} %</span>
                    </div>

                    <div class="tax-card-actions">
                        <button class="btn btn-outline-primary btn-sm flex-fill"
                                onclick='editTax(@json($tax))'
                                data-bs-toggle="modal"
                                data-bs-target="#taxModal"
                                aria-label="Editar impuesto {{ $tax->name }}">
                            <i class="fas fa-edit me-1"></i> Editar
                        </button>
                        @if($tax->status)
                            <button class="btn btn-outline-danger btn-sm"
                                    onclick="deleteTax('{{ $tax->id }}')"
                                    aria-label="Eliminar impuesto {{ $tax->name }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        @else
                            <button class="btn btn-outline-success btn-sm"
                                    onclick="activateTax('{{ $tax->id }}')"
                                    aria-label="Activar impuesto {{ $tax->name }}">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        @if($taxes->count() > 1)
        <div class="tax-slider-dots" id="taxSliderDots">
            @foreach($taxes as $tax)
            <button class="tax-dot" data-index="{{ $loop->index }}" aria-label="Ir a {{ $tax->name }}"></button>
            @endforeach
        </div>
        @endif

    @endif

</div>

@include('backend.taxes._tax_modal')
