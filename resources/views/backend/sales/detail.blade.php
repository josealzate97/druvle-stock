<div class="modal fade" id="saleDetailModal" tabindex="-1" aria-labelledby="saleDetailModalLabel" aria-hidden="true">
  
    <div class="modal-dialog modal-xl">

        <div class="modal-content p-4">

            <div class="modal-header">

                <h4 class="modal-title" id="saleDetailModalLabel">
                    <i class="fas fa-receipt color-primary"></i>&nbsp;
                    Detalle de la Orden &nbsp;&nbsp;<span class="color-primary badge bg-grey" x-text="saleDetail.code"></span>
                </h4>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

            </div>

            <div class="modal-body">

                <!-- Detalles de la orden -->
                <template x-if="saleDetail">

                    <div class="col-12">

                        <div class="row my-3">

                            <div class="col-md-3 col-sm-6 mb-2 d-flex flex-column">
                                <label class="fw-bold mb-1">Fecha de venta</label> 
                                <span x-text="saleDetail.sale_date"></span>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-2 d-flex flex-column">
                                <label class="fw-bold mb-1">Tipo de pago</label> 
                                <span class="fw-bold text-success" x-text="saleDetail.payment_type == 1 ? 'EFECTIVO' : saleDetail.payment_type == 2 ? 'BIZUM' : 'TPV'"></span>
                            </div>

                            <div class="col-md-2 col-sm-6 mb-2 d-flex flex-column">
                                <label class="fw-bold mb-1">Sub total</label> 
                                <span x-text="saleDetail.subtotal + ' €'"></span> 
                            </div>

                            <div class="col-md-2 col-sm-6 mb-2 d-flex flex-column">
                                <label class="fw-bold mb-1">Impuestos</label> 
                                <span x-text="saleDetail.tax + ' €'"></span>
                            </div>

                            <div class="col-md-2 col-sm-6 mb-2 d-flex flex-column">
                                <label class="fw-bold mb-1">Total</label> 
                                <span x-text="saleDetail.total + ' €'"></span> 
                            </div>

                        </div>

                        <hr>

                        <div class="row my-3">
                            
                            <div class="col-md-3 col-sm-6 mb-2 d-flex flex-column">  
                                <label class="fw-bold">Cliente</label>
                                <span x-text="saleDetail.client_name ? saleDetail.client_name : 'Anónimo'"></span>
                            </div>

                            <template x-if="saleDetail.client_email">

                                <div class="col-md-3 col-sm-6 mb-2 d-flex flex-column">
                                    <label class="fw-bold">Email</label>
                                    <span x-text="saleDetail.client_email"></span>
                                </div>
                                
                            </template>

                        </div>

                        <hr>

                        <div class="row my-3">

                            <h4 class="fw-bold fs-4 color-primary">
                                <i class="fas fa-box me-2"></i> Productos vendidos
                            </h4>

                            <table class="table table-striped table-hover">
                                
                                <thead>

                                    <tr class="text-success">
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
                                                <span x-text="item.name"></span>
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

                </template>

                <div x-show="!saleDetail">Cargando...</div>

                <!-- Fin de detalles de la orden -->

                <!-- Devoluciones -->
                 <template x-if="saleDetail.returns && saleDetail.returns.length > 0">
                    
                    <div class="row my-4">
                        
                        <h4 class="fw-bold fs-4 text-danger">
                            <i class="fas fa-undo me-2"></i>Devoluciones asociadas
                        </h4>
                        
                        <table class="table table-striped table-hover">

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

                </template>

            </div>

            <div class="modal-footer col-12 p-4 d-flex justify-content-center gap-20">

                <button class="btn btn-success" @click="printInvoice(saleId)">
                    <i class="fas fa-print"></i>&nbsp;Imprimir factura
                </button>

                <button class="btn btn-warning" @click="returnProducts(saleId)">
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