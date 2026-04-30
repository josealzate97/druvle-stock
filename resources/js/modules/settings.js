document.addEventListener("DOMContentLoaded", () => {

    console.log("Settings Js Loaded!");

    setupNotificationsCrud();

    // Spinner al cambiar de tab
    const tabLoader = document.getElementById('settingsTabLoader');
    const tabContent = document.getElementById('settingsTabsContent');
    const loaderText = tabLoader?.querySelector('.loader-text');

    const tabLabels = {
        'basic-tab':         'Cargando información general...',
        'taxes-tab':         'Cargando impuestos...',
        'notifications-tab': 'Cargando notificaciones...',
    };

    document.querySelectorAll('#settingsTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', () => {
            if (tabLoader && tabContent) {
                if (loaderText) loaderText.textContent = tabLabels[tab.id] ?? 'Cargando...';
                tabContent.style.opacity = '0';
                tabContent.style.pointerEvents = 'none';
                tabLoader.style.display = 'flex';
                setTimeout(() => {
                    tabLoader.style.display = 'none';
                    tabContent.style.opacity = '1';
                    tabContent.style.pointerEvents = '';
                }, 350);
            }
        });
    });
});

const notyf = new Notyf();

window.settingsForm = function(settingsData) {

    return {
        editMode: false,
        form: { ...settingsData },
        toggleEdit() {
            this.editMode = !this.editMode;
        },
        async saveSettings() {

            try {

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(`/settings/update/${this.form.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify(this.form),
                });

                if (response.ok == true) {

                    this.editMode = false;

                    // Mostrar notificación de éxito
                    notyf.success('Configuración actualizada correctamente');

                } else {

                    // Mostrar notificación de error
                    notyf.error('Error al actualizar la configuración');

                }
            } catch (error) {

                console.error('Error:', error);

                // Mostrar notificación de error inesperado
                notyf.error('Ocurrió un error inesperado');
            }
        },
    };
};

function setupNotificationsCrud() {
    const notificationForm = document.getElementById('notificationForm');
    const notificationModalElement = document.getElementById('notificationModal');
    const targetTypeSelect = document.getElementById('notificationTargetType');
    const targetRoleWrapper = document.getElementById('notificationTargetRoleWrapper');
    const targetUsersWrapper = document.getElementById('notificationTargetUsersWrapper');
    const targetRoleSelect = document.getElementById('notificationTargetRole');
    const targetUsersSelect = document.getElementById('notificationTargetUsers');

    if (!notificationForm || !notificationModalElement) {
        return;
    }

    const notificationModal = new bootstrap.Modal(notificationModalElement);
    const modalTitle = document.getElementById('notificationModalLabel');
    const titlePrefix = '<span class="modal-icon"><i class="fas fa-bell"></i></span>';

    notificationModalElement.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const mode = trigger ? trigger.getAttribute('data-bs-mode') : null;

        if (mode === 'new') {
            resetNotificationForm();
            modalTitle.innerHTML = `${titlePrefix} Crear notificación`;
        }
    });

    if (targetTypeSelect) {
        targetTypeSelect.addEventListener('change', toggleNotificationTargetFields);
    }

    notificationForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(notificationForm);
        const data = Object.fromEntries(formData.entries());
        const isEdit = !!data.id;
        const url = isEdit ? `/notifications/update/${data.id}` : '/notifications/create';

        if (!data.priority) {
            data.priority = 1;
        }

        if (!isEdit) {
            const targetType = targetTypeSelect ? targetTypeSelect.value : 'all_active';
            data.target_type = targetType;

            if (targetType === 'role') {
                data.target_role = targetRoleSelect ? targetRoleSelect.value : '';
            } else {
                delete data.target_role;
            }

            if (targetType === 'users') {
                data.user_ids = targetUsersSelect
                    ? Array.from(targetUsersSelect.selectedOptions).map((option) => option.value)
                    : [];
            } else {
                delete data.user_ids;
            }
        }

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'No se pudo guardar la notificación');
            }

            notyf.success(isEdit ? 'Notificación actualizada correctamente' : 'Notificación creada correctamente');
            notificationModal.hide();
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            notyf.error(error.message || 'Error al guardar la notificación');
        }
    });

    window.editNotification = function (notification) {
        resetNotificationForm();
        modalTitle.innerHTML = `${titlePrefix} Editar notificación`;

        document.getElementById('notificationId').value = notification.id ?? '';
        document.getElementById('notificationType').value = notification.type ?? '';
        document.getElementById('notificationTitle').value = notification.title ?? '';
        document.getElementById('notificationMessage').value = notification.message ?? '';
        document.getElementById('notificationPriority').value = notification.priority ?? 1;
        document.getElementById('notificationScheduledAt').value = toLocalDateTimeInput(notification.scheduled_at);
        document.getElementById('notificationExpiresAt').value = toLocalDateTimeInput(notification.expires_at);
        if (targetTypeSelect) {
            targetTypeSelect.value = 'all_active';
        }
        if (targetRoleSelect) {
            targetRoleSelect.value = '';
        }
        if (targetUsersSelect) {
            Array.from(targetUsersSelect.options).forEach((option) => {
                option.selected = false;
            });
        }
        toggleNotificationTargetFields();

        notificationModal.show();
    };

    window.deleteNotification = async function (notificationId) {
        if (!confirm('¿Deseas eliminar esta notificación?')) {
            return;
        }

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(`/notifications/delete/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'No se pudo eliminar la notificación');
            }

            notyf.success('Notificación eliminada correctamente');
            setTimeout(() => location.reload(), 700);
        } catch (error) {
            notyf.error(error.message || 'Error al eliminar la notificación');
        }
    };

    function toggleNotificationTargetFields() {
        const targetType = targetTypeSelect ? targetTypeSelect.value : 'all_active';
        const isRole = targetType === 'role';
        const isUsers = targetType === 'users';

        if (targetRoleWrapper) {
            targetRoleWrapper.classList.toggle('d-none', !isRole);
        }

        if (targetUsersWrapper) {
            targetUsersWrapper.classList.toggle('d-none', !isUsers);
        }
    }

    toggleNotificationTargetFields();
}

function resetNotificationForm() {
    const form = document.getElementById('notificationForm');
    if (!form) {
        return;
    }

    form.reset();
    document.getElementById('notificationId').value = '';
    document.getElementById('notificationPriority').value = 1;
    const targetTypeSelect = document.getElementById('notificationTargetType');
    const targetRoleSelect = document.getElementById('notificationTargetRole');
    const targetUsersSelect = document.getElementById('notificationTargetUsers');

    if (targetTypeSelect) {
        targetTypeSelect.value = 'all_active';
        targetTypeSelect.dispatchEvent(new Event('change'));
    }

    if (targetRoleSelect) {
        targetRoleSelect.value = '';
    }

    if (targetUsersSelect) {
        Array.from(targetUsersSelect.options).forEach((option) => {
            option.selected = false;
        });
    }
}

function toLocalDateTimeInput(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const pad = (num) => String(num).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}
