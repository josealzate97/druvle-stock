
@push('styles')
    @vite(['resources/css/modules/modals.css'])
@endpush

<div class="modal-header align-items-start">
    <div class="modal-title-block">
        <h4 class="modal-title mb-1" id="refundModalLabel">
            <i class="fas fa-undo me-2 color-primary"></i>
            Gestionar Devolución
            <span class="color-primary fw-bold">- Factura {{ $sale->code ?? '' }}</span>
        </h4>
        <div class="text-muted small">Registra la devolución por producto y su motivo.</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<div class="modal-body">

    <div class="mb-3">
        <div class="text-muted small">Selecciona los productos y cantidades a devolver.</div>
    </div>

    <form id="returnForm">

        <table class="table table-sm align-middle table-hover">

            <thead>
                <tr>
                    <th class="text-uppercase small text-muted">Producto</th>
                    <th class="text-uppercase small text-muted text-center">Vendido</th>
                    <th class="text-uppercase small text-muted text-center">Devolución</th>
                    <th class="text-uppercase small text-muted">Acción</th>
                    <th class="text-uppercase small text-muted">Nota</th>
                </tr>
            </thead>

            <tbody>

                @foreach($sale->items as $item)

                    <tr>

                        <td>
                            <div class="fw-semibold">{{ $item->producto->name ?? '' }}</div>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $item->quantity }}</span>
                        </td>

                        <td class="text-center">

                            <input type="hidden" name="sale_id[{{ $item->id }}]"
                            value="{{ $sale->id }}">

                            <input type="number" name="return_quantity[{{ $item->id }}]"
                            min="0" max="{{ $item->quantity }}"
                            value="0" class="form-control form-control-sm text-center d-inline-block"
                            style="max-width: 90px;"
                            data-price="{{ $item->producto->sale_price }}"
                            data-sale-id="{{ $sale->id }}"
                            data-sale-detail-id="{{ $item->id }}"
                            data-product-id="{{ $item->product_id }}" required>

                        </td>

                        <td>
                            <select name="return_reason[{{ $item->id }}]" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Acción tomada</option>
                                <option value="1">Reposición Producto</option>
                                <option value="2">Producto Dañado</option>
                            </select>
                        </td>

                        <td>
                            <textarea name="return_note[{{ $item->id }}]" class="form-control form-control-sm" rows="1" placeholder="Opcional"></textarea>
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <div class="mt-3 d-flex justify-content-end">
            <div class="px-3 py-2 border rounded bg-light fw-semibold">
                Total a Devolver: <span id="totalRefund">0,00 €</span>
            </div>
        </div>

    </form>

</div>

<div class="modal-footer d-flex flex-wrap justify-content-between gap-2">

    <button type="button" class="btn btn-outline-warning col-12 col-md-3" id="backToDetailBtn">
        <i class="fas fa-arrow-left"></i>&nbsp;Volver al detalle
    </button>

    <button type="button" class="btn btn-outline-success col-12 col-md-3" id="processRefundBtn">
        <i class="fas fa-check"></i>&nbsp;Procesar Devolución
    </button>

    <button type="button" class="btn btn-outline-danger col-12 col-md-3" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>&nbsp;Cancelar
    </button>

</div>

<script>



</script>
