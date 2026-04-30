
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

@include('backend.taxes._tax_modal')
