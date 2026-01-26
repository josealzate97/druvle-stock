
@section('title', 'Impuestos')

@push('scripts')
    @vite(['resources/js/modules/taxes.js'])
@endpush

<div class="table-responsive">

    <table class="table table-borderless align-middle table-striped table-hover">

        <thead class="table-light">
            <tr class="text-success">
                <th class="color-primary fw-bold">Nombre</th>
                <th class="color-primary fw-bold">Porcentaje</th>
                <th class="color-primary fw-bold">Estado</th>
                <th class="color-primary fw-bold">Acciones</th>
            </tr>
        </thead>

        <tbody>
            
            @foreach($taxes as $tax)

                <tr>

                    <td>{{ $tax->name }}</td>
                    <td>{{ number_format($tax->rate, 2) }} %</td>

                    <td>
                        @if($tax->status)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>

                    <td>

                        <button class="btn btn-primary btn-sm" onclick='editTax(@json($tax))' data-bs-toggle="modal" data-bs-target="#taxModal">
                            <i class="fas fa-edit"></i> Editar
                        </button>

                        @if($tax->status)

                            <button class="btn btn-danger btn-sm" onclick="deleteTax('{{ $tax->id }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>

                        @else

                            <button class="btn btn-success btn-sm" onclick="activateTax('{{ $tax->id }}')">
                                <i class="fas fa-check"></i> Activar
                            </button>

                        @endif
                </tr>

            @endforeach

        </tbody>

    </table>

</div>

@include('backend.taxes._tax_modal')
