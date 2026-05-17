<!-- Contenido de Historial de Ventas -->
<div class="card p-4 section-hero sales-history-hero">
    <div class="sales-history-hero-layout">
        <div class="flex-grow-1 sales-history-copy">
            <div class="sales-history-heading">
                <div class="section-hero-icon sales-history-icon">
                    <i class="fas fa-list"></i>
                </div>
                <h2 class="fw-bold mb-0">Historial de Ventas</h2>
            </div>
            <div class="text-muted small fw-bold sales-history-description">
                Consulta, reimprime facturas o gestiona devoluciones de ventas pasadas.
            </div>
        </div>
    </div>
</div>

<div class="card p-0 mt-4 section-card">

    <div class="section-toolbar sales-history-toolbar">
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

    <div class="d-none d-lg-block">
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
                            <td colspan="8">
                                <div class="sd-empty-state">
                                    <span class="sd-empty-icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <p class="sd-empty-title">Sin ventas registradas</p>
                                    <p class="sd-empty-desc">El historial de transacciones aparecerá aquí una vez que se registre la primera venta.</p>
                                </div>
                            </td>
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
                            <td>$ {{ number_format($history->subtotal, 2, ',', '.') }}</td>
                            <td>$ {{ number_format($history->tax, 2, ',', '.') }}</td>
                            <td>$ {{ number_format($history->total, 2, ',', '.') }}</td>
                            <td>
                                @if ($history->status == 1)
                                    <span class="status-pill status-pill-success">Completa</span>
                                @else
                                    <span class="status-pill status-pill-muted">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm sales-view-btn" @click="openSaleModal('{{ $history->id }}')">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver detalle</span>
                                </button>
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>
    </div>

    <div class="d-lg-none sales-history-cards" id="salesHistoryCards">
        @if ($salesHistory->isEmpty())
            <div class="sales-history-empty-cards">
                <div class="sd-empty-state">
                    <span class="sd-empty-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                    <p class="sd-empty-title">Sin ventas registradas</p>
                    <p class="sd-empty-desc">El historial de transacciones aparecerá aquí una vez que se registre la primera venta.</p>
                </div>
            </div>
        @endif

        @foreach($salesHistory as $history)
            <article class="sales-history-card" data-status="{{ $history->status }}">
                <div class="sales-history-card__top">
                    <span class="badge sale-code-badge">{{ $history->code }}</span>
                    @if ($history->status == 1)
                        <span class="status-pill status-pill-success">Completa</span>
                    @else
                        <span class="status-pill status-pill-muted">Pendiente</span>
                    @endif
                </div>

                <div class="sales-history-card__meta">
                    <div class="sales-history-card__item">
                        <span>Fecha</span>
                        <strong>{{ \Carbon\Carbon::parse($history->sale_date)->format('Y-m-d h:i A') }}</strong>
                    </div>
                    <div class="sales-history-card__item">
                        <span>Cliente</span>
                        <strong>{{ $history->client->name ?? 'Anonimo' }}</strong>
                    </div>
                    <div class="sales-history-card__item">
                        <span>Subtotal</span>
                        <strong>$ {{ number_format($history->subtotal, 2, ',', '.') }}</strong>
                    </div>
                    <div class="sales-history-card__item">
                        <span>Impuestos</span>
                        <strong>$ {{ number_format($history->tax, 2, ',', '.') }}</strong>
                    </div>
                    <div class="sales-history-card__item sales-history-card__item--total">
                        <span>Total</span>
                        <strong>$ {{ number_format($history->total, 2, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="sales-history-card__actions">
                    <button class="btn btn-sm sales-view-btn w-100" @click="openSaleModal('{{ $history->id }}')">
                        <i class="fas fa-eye"></i>
                        <span>Ver detalle</span>
                    </button>
                </div>
            </article>
        @endforeach

        <div class="sales-history-empty sales-history-empty-cards" id="salesHistoryEmptyCards" style="display:none;">
            <div class="sd-empty-state">
                <span class="sd-empty-icon">
                    <i class="fas fa-search"></i>
                </span>
                <p class="sd-empty-title">Sin resultados</p>
                <p class="sd-empty-desc">No se encontraron ventas con los filtros aplicados.</p>
            </div>
        </div>
    </div>

    <div class="section-footer">
        @if (method_exists($salesHistory, 'links'))
            {{ $salesHistory->links('pagination::bootstrap-5') }}
        @endif
    </div>

</div>
