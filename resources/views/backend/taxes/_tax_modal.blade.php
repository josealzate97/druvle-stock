
@push('scripts')
    @vite(['resources/js/modules/taxes.js'])
@endpush

<div class="modal fade" id="taxModal" tabindex="-1" aria-labelledby="taxModalLabel" aria-hidden="true">
    
    <form id="taxForm">

        <div class="modal-dialog">

            <div class="modal-content p-3">

                <div class="modal-header">
                    
                    <h4 class="modal-title" id="taxModalLabel">
                        <i class="fas fa-edit me-2 color-primary"></i>
                        Editar Impuestos
                    </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <input type="hidden" id="taxId" name="id">

                    <div class="mb-3">
                        <label for="taxName" class="form-label fw-bold">Nombre</label>
                        <input type="text" class="form-control" id="taxName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="taxRate" class="form-label fw-bold">Porcentaje (%)</label>
                        <input type="number" step="0.01" class="form-control" id="taxRate" name="rate" required>
                    </div>

                    <div class="mb-3">
                        <label for="taxStatus" class="form-label fw-bold">Estado</label>
                        <select class="form-select" id="taxStatus" name="status" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer col-12 d-flex flex-wrap justify-content-center">

                    <button type="button" class="btn btn-danger btn-md col-5" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-success btn-md col-5">
                        <i class="fas fa-save me-2"></i>
                        Guardar
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>