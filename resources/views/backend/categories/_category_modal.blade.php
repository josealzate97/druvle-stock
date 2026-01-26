<!-- Modal Crear/Editar Categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
  
    <div class="modal-dialog">

    <form id="categoryForm">

      <div class="modal-content">

        <div class="modal-header">

          <h4 class="modal-title" id="categoryModalLabel">
              <i class="fas fa-circle-plus me-2 color-primary"></i>
              Nueva Categoría
          </h4>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        
        </div>

        <div class="modal-body col-12 d-flex flex-wrap">

          <input type="hidden" id="categoryId" name="id" value="">

          <div class="mb-3 col-12">
            <label for="categoryName" class="form-label fw-bold">
              Nombre Categoría&nbsp;<span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="categoryName" name="name"  maxlength="50" placeholder="Nombre Categoría" required>
          </div>

          <div class="mb-3 col-12">
            <label for="categoryAbbr" class="form-label fw-bold">
              Abreviación&nbsp;<span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="categoryAbbr" name="abbreviation" maxlength="4" placeholder="Abreviación" required>
          </div>

          <div class="mb-3 col-lg-6 col-md-6 col-sm-12">

            <label for="categoryIcon" class="form-label fw-bold">Icono</label>
            <select class="form-select" id="categoryIcon" name="icon" required>

              <option value="" selected disabled>Selecciona un icono</option>
              <option value="fa-wine-bottle"><i class="fa fa-wine-bottle"></i> Bebidas</option>
              <option value="fa-martini-glass"><i class="fa fa-martini-glass"></i> Licor</option>
              <option value="fa-candy-cane"><i class="fa fa-candy-cane"></i> Chucherias</option>
              <option value="fa-vector-square"><i class="fa fa-vector-square"></i> Estampados</option>
              <option value="fa-tshirt"><i class="fa fa-tshirt"></i> Camisetas</option>
              <option value="fa-baby"><i class="fa fa-baby"></i> Jugueteria</option>
              <option value="fa-handshake"><i class="fa fa-handshake"></i> Servicios</option>
              <option value="fa-globe"><i class="fa fa-globe"></i> Servicios Digitales</option>
              <option value="fa-hand-sparkles"><i class="fa fa-hand-sparkles"></i> Limpieza</option>

            </select>

          </div>
          

          <div class="mb-3 col-6 d-flex flex-wrap justify-content-center text-center">
            <label for="categoryColor" class="form-label fw-bold col-12">Color</label>
            <input type="color" class="form-control form-control-color" id="categoryColor" name="color" value="#563d7c" title="Elige un color">
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

    </form>

  </div>

</div>