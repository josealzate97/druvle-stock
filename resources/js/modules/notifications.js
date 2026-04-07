document.addEventListener("DOMContentLoaded", () => {
    const notyf = new Notyf();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const markAllButton = document.getElementById('markAllInboxNotificationsBtn');
    const markOneButtons = document.querySelectorAll('.mark-one-read-btn');

    if (!csrfToken) {
        return;
    }

    markOneButtons.forEach((button) => {
        button.addEventListener('click', async (event) => {
            const row = event.currentTarget.closest('tr[data-user-notification-id]');
            if (!row) {
                return;
            }

            const userNotificationId = row.dataset.userNotificationId;

            try {
                const response = await fetch(`/notifications/${userNotificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'No se pudo marcar la notificación');
                }

                notyf.success('Notificación marcada como leída');
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                notyf.error(error.message || 'Error al actualizar notificación');
            }
        });
    });

    if (markAllButton) {
        markAllButton.addEventListener('click', async () => {
            if (markAllButton.disabled) {
                return;
            }

            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'No se pudieron marcar todas');
                }

                notyf.success('Notificaciones marcadas como leídas');
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                notyf.error(error.message || 'Error al actualizar notificaciones');
            }
        });
    }
});

