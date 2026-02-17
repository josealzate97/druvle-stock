
@push('styles')
  @vite(['resources/css/modules/modals.css'])
@endpush

<!-- Modal Crear/Editar Producto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  
    <div class="modal-dialog modal-lg">

        <form id="productForm">

            <div class="modal-content form-modal">

                <div class="modal-header">

                    <div class="modal-title-block">
                        <h4 class="modal-title" id="productModalLabel">
                            <i class="fas fa-circle-plus me-2 color-primary"></i>
                            Nuevo Producto
                        </h4>
                        <span class="modal-subtitle">Completa los datos básicos, precios y stock.</span>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                
                </div>

                <div class="modal-body row g-3">

                    <input type="hidden" name="id" id="productId" value="">

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productName" class="form-label fw-bold">
                            Nombre&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="productName" name="name" maxlength="50" placeholder="Nombre del producto" required>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productCode" class="form-label fw-bold">
                            Código&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="productCode" name="code" maxlength="20" placeholder="Código del producto" required>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productCategory" class="form-label fw-bold">
                            Categoría&nbsp;<span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="productCategory" name="category_id" required>
                            <option value="" disabled selected>Selecciona una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productSale" class="form-label fw-bold">
                            Precio Compra&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control mask-money" id="productPrice" name="purchase_price" placeholder="Precio de compra" required>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productPrice" class="form-label fw-bold">
                            Precio Venta&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control mask-money" id="productSale" name="sale_price" placeholder="Precio de venta" required>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productQuantity" class="form-label fw-bold">
                            Cantidad&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="productQuantity" name="quantity" placeholder="Cantidad" required>
                    </div>

                    <div class="col-12">
                        <label for="productNote" class="form-label fw-bold">
                            Notas
                        </label>
                        <textarea class="form-control" id="productNotes" name="notes" rows="2" placeholder="Notas adicionales sobre el producto"></textarea>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="productTax" class="form-label fw-bold">Aplica IVA?</label>
                        <div class="form-check form-switch form-switch-lg ms-2">
                            <input class="form-check-input custom-switch-success" type="checkbox" id="productTaxSwitch" name="taxable">
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12" id="taxDropdownContainer" style="display: none;">
                        <label for="productTax" class="form-label fw-bold">IVA</label>
                        <select class="form-select" id="productTax" name="tax_id">
                            <option value="" selected disabled>Selecciona un IVA</option>
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}">{{ number_format($tax->rate, 2) }} %</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer col-12 d-flex justify-content-between gap-2 my-4">

                    <button type="button" class="btn btn-outline-danger col-5" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-outline-success col-5">
                        <i class="fas fa-save me-2"></i>
                        Guardar
                    </button>

                </div>

            </div>

        </form>

    </div>
  
</div>
