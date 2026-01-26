
<div class="modal-header">

    <h4 class="modal-title" id="refundModalLabel">
        Gestionar Devolución - Factura <span class="color-primary fw-bold">{{ $sale->code ?? '' }}</span>
    </h4>

    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

</div>

<div class="modal-body">

    <div class="mb-2 text-muted">
        Selecciona los productos y cantidades a devolver.
    </div>

    <form id="returnForm">

        <table class="table">

            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Vendido</th>
                    <th>Devolucion</th>
                    <th>Accion</th>
                    <th>Nota</th>
                </tr>
            </thead>

            <tbody>

                @foreach($sale->items as $item)

                    <tr>

                        <td>{{ $item->producto->name ?? '' }}</td>

                        <td class="text-center">{{ $item->quantity }}</td>

                        <td class="text-center">

                            <input type="hidden" name="sale_id[{{ $item->id }}]"
                            value="{{ $sale->id }}">

                            <input type="number" name="return_quantity[{{ $item->id }}]"
                            min="0" max="{{ $item->quantity }}"
                            value="0" class="form-control text-center"
                            style="width: 80px; display: inline-block;"
                            data-price="{{ $item->producto->sale_price }}"
                            data-sale-id="{{ $sale->id }}"
                            data-sale-detail-id="{{ $item->id }}"
                            data-product-id="{{ $item->product_id }}" required>

                        </td>

                        <td>
                            <select name="return_reason[{{ $item->id }}]" class="form-select" required>
                                <option value="" disabled>Accion tomada</option>
                                <option value="1">Reposición Producto</option>
                                <option value="2">Producto Dañado</option>
                            </select>
                        </td>

                        <td>
                            <textarea name="return_note[{{ $item->id }}]" class="form-control" rows="1"></textarea>
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <div class="mt-3 fw-bold fs-5 text-end">
            Total a Devolver: <span id="totalRefund">0,00 €</span>
        </div>

    </form>

</div>

<div class="modal-footer d-flex justify-content-center gap-2">

    <button type="button" class="btn btn-warning" id="backToDetailBtn">
        <i class="fas fa-arrow-left"></i>&nbsp;Volver al detalle
    </button>

    <button type="button" class="btn btn-success" id="processRefundBtn">
        <i class="fas fa-check"></i>&nbsp;Procesar Devolución
    </button>

    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
        <i class="fas fa-times"></i>&nbsp;Cancelar
    </button>

</div>

<script>



</script>
