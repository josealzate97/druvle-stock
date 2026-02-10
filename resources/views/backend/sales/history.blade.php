<!-- Contenido de Historial de Ventas -->
<div class="card p-4 section-hero">
    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
        <div class="section-hero-icon">
            <i class="fas fa-list"></i>
        </div>
        <div class="flex-grow-1">
            <h2 class="fw-bold mb-0">Historial de Ventas</h2>
            <div class="text-muted small fw-bold">
                Consulta, reimprime facturas o gestiona devoluciones de ventas pasadas.
            </div>
        </div>
    </div>
</div>

<div class="card p-0 mt-4 section-card">

    <div class="section-toolbar">
        <div class="section-search">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control form-control-sm" id="salesHistorySearch" placeholder="Buscar venta...">
        </div>
        <select class="form-select form-select-sm section-filter" id="salesHistoryStatus">
            <option value="">Todos los estados</option>
            <option value="1">Completas</option>
            <option value="0">Pendientes</option>
        </select>
    </div>

    <div class="table-responsive">

        <table class="table table-borderless align-middle section-table" id="salesHistoryTable">

            <thead>
                <tr>
                    <th>Factura #</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Subtotal</th>
                    <th>Impuestos</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @if ($salesHistory->isEmpty())
                    <tr class="sales-history-empty">
                        <td colspan="8" class="text-center text-muted fw-bold fs-6 my-4">No hay ventas registradas.</td>
                    </tr>
                @endif

                @foreach($salesHistory as $history)

                    <tr data-id="{{ $history->sale->id ?? '-' }}" data-status="{{ $history->status }}">
                        <td>
                            <a href="#" class="text-decoration-none"
                               @click="openSaleModal('{{ $history->id }}')">
                                <span class="badge sale-code-badge">{{ $history->code }}</span>
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($history->sale_date)->format('Y-m-d h:i A') }}</td>
                        <td>{{ $history->client->name ?? 'Anonimo' }}</td>
                        <td>{{ number_format($history->subtotal, 2) }} €</td>
                        <td>{{ number_format($history->tax, 2) }} €</td>
                        <td>{{ number_format($history->total, 2) }} €</td>
                        <td>
                            @if ($history->status == 1)
                                <span class="status-pill status-pill-success">Completa</span>
                            @else
                                <span class="status-pill status-pill-muted">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button class="btn btn-icon text-primary" @click="openSaleModal('{{ $history->id }}')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <div class="section-footer">
        @if (method_exists($salesHistory, 'links'))
            {{ $salesHistory->links('pagination::bootstrap-5') }}
        @endif
    </div>

</div>
