document.addEventListener("DOMContentLoaded", () => {

    console.log("Tenants JS Loaded!");

    const searchInput = document.getElementById('tenantsSearch');
    const statusSelect = document.getElementById('tenantsStatusFilter');
    const table = document.querySelector('.section-table');

    if (searchInput && statusSelect && table) {
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const status = statusSelect.value;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                const matchesStatus = !status || row.dataset.status === status;
                row.style.display = matchesQuery && matchesStatus ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterRows);
        statusSelect.addEventListener('change', filterRows);
    }

    const tenantForm = document.getElementById('tenantForm');
    const modalTitle = document.getElementById('tenantModalLabel');
    const tenantModalElement = document.getElementById('tenantModal');
    const tenantModal = new bootstrap.Modal(tenantModalElement);
    const tenantIdInput = document.getElementById('tenantId');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    tenantModalElement.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const mode = trigger ? trigger.getAttribute('data-bs-mode') : null;

        if (mode === 'new') {
            clearTenantModal();
        }
    });

    tenantModalElement.addEventListener('hidden.bs.modal', function () {
        // Restaurar formulario si se mostraron credenciales
        const credBlock = document.getElementById('tenantCredentialsBlock');
        const form = document.getElementById('tenantForm');
        if (!credBlock.classList.contains('d-none')) {
            credBlock.classList.add('d-none');
            form.classList.remove('d-none');
        }
    });

    // Generar slug automático a partir del nombre
    document.getElementById('tenantName').addEventListener('input', function () {
        const slugInput = document.getElementById('tenantSlug');
        if (!tenantIdInput.value) {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });

    tenantForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const url = data.id ? `/tenants/update/${data.id}` : '/tenants/create';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok && result.success) {
                if (!data.id && result.admin) {
                    // Mostrar credenciales del admin creado automáticamente
                    document.getElementById('tenantForm').closest('form').classList.add('d-none');
                    const credBlock = document.getElementById('tenantCredentialsBlock');
                    credBlock.classList.remove('d-none');
                    document.getElementById('credUsername').value = result.admin.username;
                    document.getElementById('credPassword').value = result.admin.password;
                    // Recargar al cerrar el modal
                    document.getElementById('tenantModal').addEventListener('hidden.bs.modal', () => location.reload(), { once: true });
                } else {
                    notyf.success('Negocio actualizado correctamente');
                    tenantModal.hide();
                    setTimeout(() => location.reload(), 800);
                }
            } else {
                const errors = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                notyf.error(errors || 'Error al guardar el negocio');
            }
        } catch (e) {
            notyf.error('Error de conexión');
        }
    });

});

// Carga datos del tenant en el modal para edición
window.editTenant = async function (id) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const tenantModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('tenantModal'));

    try {
        const response = await fetch(`/tenants/getTenant/${id}`, {
            headers: { 'X-CSRF-TOKEN': token },
        });

        const result = await response.json();

        if (result.success) {
            const t = result.tenant;
            document.getElementById('tenantId').value = t.id;
            document.getElementById('tenantName').value = t.name;
            document.getElementById('tenantSlug').value = t.slug;
            document.getElementById('tenantPlan').value = t.plan;
            document.getElementById('tenantTrialEndsAt').value = t.trial_ends_at
                ? t.trial_ends_at.substring(0, 10)
                : '';

            document.getElementById('tenantModalLabel').innerHTML =
                '<span class="modal-icon"><i class="fas fa-building"></i></span> Editar Negocio';

            tenantModal.show();
        }
    } catch (e) {
        notyf.error('Error al cargar el negocio');
    }
};

window.deleteTenant = async function (id) {
    if (!confirm('¿Deseas desactivar este negocio?')) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/tenants/delete/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
        });

        const result = await response.json();

        if (result.success) {
            notyf.success('Negocio desactivado');
            setTimeout(() => location.reload(), 800);
        }
    } catch (e) {
        notyf.error('Error al desactivar el negocio');
    }
};

window.activateTenant = async function (id) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/tenants/activate/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
        });

        const result = await response.json();

        if (result.success) {
            notyf.success('Negocio activado');
            setTimeout(() => location.reload(), 800);
        }
    } catch (e) {
        notyf.error('Error al activar el negocio');
    }
};

function clearTenantModal() {
    document.getElementById('tenantId').value = '';
    document.getElementById('tenantName').value = '';
    document.getElementById('tenantSlug').value = '';
    document.getElementById('tenantPlan').value = '1';
    document.getElementById('tenantTrialEndsAt').value = '';
    document.getElementById('tenantModalLabel').innerHTML =
        '<span class="modal-icon"><i class="fas fa-building"></i></span> Nuevo Negocio';
}
