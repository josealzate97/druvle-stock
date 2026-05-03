<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content form-modal">
      <div class="modal-header">
        <div class="modal-title-block">
          <h4 class="modal-title" id="productDetailsModalLabel">
            <span class="modal-icon">
              <i class="fas fa-circle-info"></i>
            </span>
            Detalle del Producto
          </h4>
          <span class="modal-subtitle">Información general, precios y stock.</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body product-details-modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="product-details-item">
              <small>Nombre</small>
              <strong id="productDetailsName">-</strong>
            </div>
          </div>
          <div class="col-md-6">
            <div class="product-details-item">
              <small>Código</small>
              <strong id="productDetailsCode">-</strong>
            </div>
          </div>
          <div class="col-md-6">
            <div class="product-details-item">
              <small>Categoría</small>
              <strong id="productDetailsCategory">-</strong>
            </div>
          </div>
          <div class="col-md-6">
            <div class="product-details-item">
              <small>Estado</small>
              <strong id="productDetailsStatus">-</strong>
            </div>
          </div>
          <div class="col-md-6">
            <div class="product-details-item">
              <small>Impuesto</small>
              <strong id="productDetailsTax">-</strong>
            </div>
          </div>
          <div class="col-md-6">
            <div class="product-details-item">
              <small>Tipo</small>
              <strong id="productDetailsType">-</strong>
            </div>
          </div>
          <div class="col-12" id="productDetailsBaseStockBlock">
            <div class="product-details-item product-details-item--highlight">
              <small>Precio de venta / Cantidad</small>
              <strong id="productDetailsPriceQty">-</strong>
            </div>
          </div>
          <div class="col-12">
            <div class="product-details-item">
              <small>Notas</small>
              <div id="productDetailsNotes" class="product-details-notes">-</div>
            </div>
          </div>
        </div>

        <div id="productDetailsSizesSection" class="product-details-sizes" style="display:none;">
          <div class="table-responsive">
            <table class="table table-sm align-middle product-details-table">
              <thead>
                <tr>
                  <th>Talla</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody id="productDetailsSizesBody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="modal-footer product-details-footer">
        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
