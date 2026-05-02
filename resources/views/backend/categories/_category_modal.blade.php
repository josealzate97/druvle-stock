@push('styles')
  @vite(['resources/css/modules/modals.css'])
@endpush

<!-- Modal Crear/Editar Categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
  
    <div class="modal-dialog">

    <form id="categoryForm">

      <div class="modal-content form-modal">

        <div class="modal-header">

          <div class="modal-title-block">
            <h4 class="modal-title" id="categoryModalLabel">
                <span class="modal-icon">
                  <i class="fas fa-tags"></i>
                </span>
                Nueva Categoría
            </h4>
            <span class="modal-subtitle">Define nombre, abreviación y estilo visual.</span>
          </div>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        
        </div>

        <div class="modal-body row g-3">

          <input type="hidden" id="categoryId" name="id" value="">

          <div class="col-12">
            <label for="categoryName" class="form-label fw-bold">
              Nombre Categoría&nbsp;<span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="categoryName" name="name"  maxlength="50" placeholder="Nombre Categoría" required>
          </div>

          <div class="col-12">
            <label for="categoryAbbr" class="form-label fw-bold">
              Abreviación&nbsp;<span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="categoryAbbr" name="abbreviation" maxlength="4" placeholder="Abreviación" required>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12">

            <label for="categoryIcon" class="form-label fw-bold">Icono</label>
            <select class="form-select" id="categoryIcon" name="icon" required>

              <option value="" selected disabled>Selecciona un icono</option>
              <option value="fa-wheat-awn"><i class="fa fa-wheat-awn"></i> Abarrotes </option>
              <option value="fa-wine-bottle"><i class="fa fa-wine-bottle"></i> Bebidas </option>
              <option value="fa-martini-glass"><i class="fa fa-martini-glass"></i> Licor </option>
              <option value="fa-candy-cane"><i class="fa fa-candy-cane"></i> Dulces </option>
              <option value="fa-paw"><i class="fa fa-paw"></i> Mascotas </option>
              <option value="fa-bread-slice"><i class="fa fa-bread-slice"></i> Panaderia </option>
              <option value="fa-baby"><i class="fa fa-baby"></i> Miscelania </option>
              <option value="fa-smoking"><i class="fa fa-smoking"></i> Cigarrillos </option>
              <option value="fa-jar"><i class="fa fa-jar"></i> Lacteos & Refrigerados </option>
              <option value="fa-globe"><i class="fa fa-globe"></i> Servicios Digitales </option>
              <option value="fa-hand-sparkles"><i class="fa fa-hand-sparkles"></i> Limpieza & Aseo </option>

            </select>

          </div>
          

          <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="categoryColor" class="form-label fw-bold col-12">Color</label>
            <div class="color-picker">
              <input type="color" class="form-control form-control-color" id="categoryColor" name="color" value="#563d7c" title="Elige un color">
              <span class="color-hint">Elige un color representativo</span>
            </div>
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
