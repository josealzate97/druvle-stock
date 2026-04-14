@push('styles')
    @vite(['resources/css/modules/modals.css'])
@endpush

<!-- Modal Crear/Editar Negocio -->
<div class="modal fade" id="tenantModal" tabindex="-1" aria-labelledby="tenantModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <form id="tenantForm">

            <div class="modal-content form-modal">

                <div class="modal-header">

                    <div class="modal-title-block">
                        <h4 class="modal-title" id="tenantModalLabel">
                            <span class="modal-icon">
                                <i class="fas fa-building"></i>
                            </span>
                            Nuevo Negocio
                        </h4>
                        <span class="modal-subtitle">Registra los datos principales del negocio.</span>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                </div>

                <div class="modal-body row g-3">

                    <input type="hidden" id="tenantId" name="id" value="">

                    <div class="col-12">
                        <label for="tenantName" class="form-label fw-bold">
                            Nombre del Negocio&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tenantName" name="name"
                            maxlength="150" placeholder="Ej: Mi Tienda" required>
                    </div>

                    <div class="col-12">
                        <label for="tenantSlug" class="form-label fw-bold">
                            Slug&nbsp;<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tenantSlug" name="slug"
                            maxlength="100" placeholder="Ej: mi-tienda" required
                            pattern="^[a-z0-9\-]+$"
                            title="Solo letras minúsculas, números y guiones">
                        <div class="form-text">Solo minúsculas, números y guiones (ej: <code>mi-tienda</code>).</div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <label for="tenantPlan" class="form-label fw-bold">
                            Plan&nbsp;<span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="tenantPlan" name="plan" required>
                            <option value="1">Free</option>
                            <option value="2">Basic</option>
                            <option value="3">Pro</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12">
                        <label for="tenantTrialEndsAt" class="form-label fw-bold">
                            Prueba hasta
                        </label>
                        <input type="date" class="form-control" id="tenantTrialEndsAt" name="trial_ends_at">
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

        <!-- Bloque de credenciales generadas (sólo al crear) -->
        <div class="modal-content form-modal d-none" id="tenantCredentialsBlock">
            <div class="modal-header">
                <div class="modal-title-block">
                    <h4 class="modal-title">
                        <span class="modal-icon"><i class="fas fa-key"></i></span>
                        Negocio creado
                    </h4>
                    <span class="modal-subtitle">Guarda estas credenciales del administrador antes de cerrar.</span>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex gap-2 align-items-start mb-3">
                    <i class="fas fa-triangle-exclamation mt-1"></i>
                    <span>Estas credenciales <strong>no se volverán a mostrar</strong>. Cópialas ahora.</span>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Usuario</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="credUsername" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('credUsername').value)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Contraseña</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="credPassword" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('credPassword').value)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i> Listo, ya lo guardé
                </button>
            </div>
        </div>

    </div>

</div>
