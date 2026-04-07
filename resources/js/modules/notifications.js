document.addEventListener("DOMContentLoaded", () => {
    const notyf = new Notyf();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const markAllButton = document.getElementById('markAllInboxNotificationsBtn');
    const markOneButtons = document.querySelectorAll('.mark-one-read-btn');
    const archiveButtons = document.querySelectorAll('.archive-one-btn');
    const detailButtons = document.querySelectorAll('.toggle-notification-detail-btn');
    const retryFailedJobsBtn = document.getElementById('retryFailedJobsBtn');

    if (!csrfToken) {
        return;
    }

    detailButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            const row = event.currentTarget.closest('tr[data-user-notification-id]');
            if (!row) {
                return;
            }

            const userNotificationId = row.dataset.userNotificationId;
            const detailRow = document.querySelector(`.notification-detail-row[data-detail-for="${userNotificationId}"]`);
            const icon = event.currentTarget.querySelector('i');

            if (!detailRow) {
                return;
            }

            detailRow.classList.toggle('d-none');
            icon?.classList.toggle('fa-chevron-down');
            icon?.classList.toggle('fa-chevron-up');
        });
    });

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
                setTimeout(() => location.reload(), 350);
            } catch (error) {
                notyf.error(error.message || 'Error al actualizar notificación');
            }
        });
    });

    archiveButtons.forEach((button) => {
        button.addEventListener('click', async (event) => {
            const row = event.currentTarget.closest('tr[data-user-notification-id]');
            if (!row) {
                return;
            }

            const userNotificationId = row.dataset.userNotificationId;

            try {
                const response = await fetch(`/notifications/${userNotificationId}/archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'No se pudo archivar la notificación');
                }

                const detailRow = document.querySelector(`.notification-detail-row[data-detail-for="${userNotificationId}"]`);
                detailRow?.remove();
                row.remove();
                notyf.success('Notificación archivada');
            } catch (error) {
                notyf.error(error.message || 'Error al archivar notificación');
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
                setTimeout(() => location.reload(), 350);
            } catch (error) {
                notyf.error(error.message || 'Error al actualizar notificaciones');
            }
        });
    }

    if (retryFailedJobsBtn) {
        retryFailedJobsBtn.addEventListener('click', async () => {
            retryFailedJobsBtn.disabled = true;
            try {
                const response = await fetch('/notifications/failed-jobs/retry', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ limit: 20 }),
                });

                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'No se pudo reintentar failed jobs');
                }

                notyf.success(`Reintento enviado (${result.retried ?? 0})`);
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                retryFailedJobsBtn.disabled = false;
                notyf.error(error.message || 'Error reintentando jobs');
            }
        });
    }
});
