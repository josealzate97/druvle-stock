
@push('styles')
    @vite(['resources/css/modules/modals.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/taxes.js'])
@endpush

<div class="modal fade" id="taxModal" tabindex="-1" aria-labelledby="taxModalLabel" aria-hidden="true">
    
    <form id="taxForm">

        <div class="modal-dialog">

            <div class="modal-content form-modal">

                <div class="modal-header">
                    
                    <div class="modal-title-block">
                        <h4 class="modal-title" id="taxModalLabel">
                            <span class="modal-icon">
                                <i class="fas fa-percent"></i>
                            </span>
                            Impuestos
                        </h4>
                        <span class="modal-subtitle">Crea o actualiza los porcentajes del sistema.</span>
                    </div>

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

                <div class="modal-footer col-12 justify-content-between gap-2 my-3">

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

        </div>

    </form>

</div>
