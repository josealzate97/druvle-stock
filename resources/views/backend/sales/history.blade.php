<!-- Contenido de Historial de Ventas -->
<div class="card p-4">

    <h4 class="fw-bold">
        <i class="fas fa-list me-2 color-primary"></i>
        Historial de Ventas
    </h4>

    <span class="text-muted fw-bold small">
        Consulta, reimprime facturas o gestiona devoluciones de ventas pasadas.
    </span>

    <hr>

    <div class="table-responsive">

        <table class="table table-borderless align-middle table-striped table-hover">

            <thead class="table-light">
                <tr class="text-success">
                    <th class="color-primary fw-bold">Factura #</th>
                    <th class="color-primary fw-bold">Fecha</th>
                    <th class="color-primary fw-bold">Cliente</th>
                    <th class="color-primary fw-bold">Subtotal </th>
                    <th class="color-primary fw-bold">Impuestos </th>
                    <th class="color-primary fw-bold">Total </th>
                    <th class="color-primary fw-bold">Estado</th>
                    <th class="color-primary fw-bold">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @if ($salesHistory->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center text-muted fw-bold fs-4 my-5">No hay ventas registradas.</td>
                    </tr>
                @endif

                @foreach($salesHistory as $history)

                    <tr data-id="{{ $history->sale->id ?? '-' }}">
                        <td><a href="#" class="fw-bold text-primary small text-decoration-underline"
                            @click="openSaleModal('{{ $history->id }}')">
                                {{ $history->code }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($history->sale_date)->format('Y-m-d h:i A') }}</td>
                        <td>{{ $history->client->name ?? 'Anonimo' }}</td>
                        <td>{{ number_format($history->subtotal, 2) }} €</td>
                        <td>{{ number_format($history->tax, 2) }} €</td>
                        <td>{{ number_format($history->total, 2) }} €</td>
                        <td><span class="badge {{ $history->status == 1 ? 'bg-success' : 'bg-warning' }}">{{ $history->status == 1 ? 'Completa' : 'Pendiente' }}</span> </td>
                        <td>
                            <button class="btn btn-warning btn-sm fw-bold" 
                            @click="openSaleModal('{{ $history->id }}')">
                                <i class="fas fa-eye me-2"></i> Detalle
                            </button>
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

