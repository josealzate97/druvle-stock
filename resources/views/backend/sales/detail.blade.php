@push('styles')
    @vite(['resources/css/modules/modals.css', 'resources/css/modules/sale-detail.css'])
@endpush

<div class="modal fade" id="saleDetailModal" tabindex="-1" aria-labelledby="saleDetailModalLabel" aria-hidden="true">
  
    <div class="modal-dialog modal-xl">

        <div class="modal-content form-modal sale-detail-modal">

            <div class="modal-header">

                <div class="modal-title-block">
                    <h4 class="modal-title" id="saleDetailModalLabel">
                        <i class="fas fa-receipt me-2 color-primary"></i>
                        Detalle de la Orden
                        <span class="badge bg-primary ms-2 small" x-text="saleDetail.code"></span>
                    </h4>
                    <span class="modal-subtitle">Vista general de la venta, cliente y productos.</span>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

            </div>

            <div class="modal-body">

                <!-- Detalles de la orden -->
                <template x-if="saleDetail">

                    <div class="col-12 sale-detail-body">

                        <div class="sale-detail-summary">
                            <div class="summary-card">
                                <span>Fecha de venta</span>
                                <strong x-text="saleDetail.sale_date"></strong>
                            </div>
                            <div class="summary-card">
                                <span>Tipo de pago</span>
                                <strong class="text-success" x-text="saleDetail.payment_type == 1 ? 'EFECTIVO' : saleDetail.payment_type == 2 ? 'BIZUM' : 'TPV'"></strong>
                            </div>
                            <div class="summary-card">
                                <span>Sub total</span>
                                <strong x-text="saleDetail.subtotal + ' €'"></strong>
                            </div>
                            <div class="summary-card">
                                <span>Impuestos</span>
                                <strong x-text="saleDetail.tax + ' €'"></strong>
                            </div>
                            <div class="summary-card total-card">
                                <span>Total</span>
                                <strong x-text="saleDetail.total + ' €'"></strong>
                            </div>
                        </div>

                        <div class="sale-detail-client">
                            <div>
                                <label>Cliente</label>
                                <span x-text="saleDetail.client_name ? saleDetail.client_name : 'Anónimo'"></span>
                            </div>

                            <template x-if="saleDetail.client_email">

                                <div>
                                    <label>Email</label>
                                    <span x-text="saleDetail.client_email"></span>
                                </div>
                                
                            </template>
                        </div>

                        <div class="sale-detail-section">
                            <h4 class="sale-detail-section-title">
                                <i class="fas fa-box me-2"></i> Productos vendidos
                            </h4>

                            <div class="table-responsive">
                                <table class="table table-borderless align-middle section-table">
                                    
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Impuesto</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <template x-for="(item, index) in saleDetail.items" :key="index">

                                            <tr>
                                                <td>
                                                    <span class="fw-bold" x-text="item.name"></span>
                                                </td>
                                                <td>
                                                    <span x-text="item.quantity"></span>
                                                </td>
                                                <td>
                                                    <span x-text="item.sale_price + ' €'"></span>
                                                </td>

                                                <td>
                                                    <span x-text="item.tax + ' % -  ' + item.tax_value + ' €'"></span>
                                                </td>

                                                <td>
                                                    <span x-text="(Number(item.total) + Number(item.tax_value)).toFixed(2) + ' €'"></span>
                                                </td>
                                            </tr>

                                        </template>

                                    </tbody>

                                </table>
                            </div>

                        </div>

                    </div>

                </template>

                <div x-show="!saleDetail" class="text-center my-4">
                    <div class="reports-loader">
                        <span class="loader-spinner"></span>
                        <span class="loader-text">Cargando detalle de venta...</span>
                    </div>
                </div>

                <!-- Fin de detalles de la orden -->

                <!-- Devoluciones -->
                 <template x-if="saleDetail.returns && saleDetail.returns.length > 0">
                    
                    <div class="sale-detail-section mt-4">
                        
                        <h4 class="sale-detail-section-title text-danger">
                            <i class="fas fa-undo me-2"></i>Devoluciones asociadas
                        </h4>
                        
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle section-table">

                            <thead>
                                <tr class="text-success">
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Razón</th>
                                    <th>Nota</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>

                            <tbody>

                                <template x-for="(ret, idx) in saleDetail.returns" :key="idx">
                                    
                                <tr>
                                        <td>
                                            <span x-text="getProductNameBySaleDetailId(ret.sale_detail_id)"></span>
                                        </td>
                                        <td x-text="ret.quantity"></td>
                                        <td>
                                            <span class="fw-bold text-success" x-text="ret.reason == 1 ? 'Reposición' : (ret.reason == 2 ? 'Dañado' : ret.reason)"></span>
                                        </td>
                                        <td x-text="ret.note"></td>
                                        <td x-text="ret.total + ' €'"></td>
                                        <td x-text="ret.created_at"></td>
                                    </tr>

                                </template>

                            </tbody>

                            </table>
                        </div>

                    </div>

                </template>

            </div>

            <div class="modal-footer col-12 d-flex justify-content-between gap-2">

                <button class="btn btn-outline-success col-5" @click="printInvoice(saleId)">
                    <i class="fas fa-print"></i>&nbsp;Imprimir factura
                </button>

                <button class="btn btn-outline-warning col-5" @click="returnProducts(saleId)">
                    <i class="fas fa-undo"></i>&nbsp;Devolución de productos
                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content p-4" id="refundModalContent"></div>
    </div>
</div>
