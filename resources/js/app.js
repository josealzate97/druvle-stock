import './sidebar-toggle';
import * as bootstrap from 'bootstrap';
import { Notyf } from 'notyf';
import Alpine from 'alpinejs';
import IMask from 'imask';

window.Notyf = Notyf;
window.IMask = IMask;
window.Alpine = Alpine;
window.bootstrap = bootstrap;

// Crear y mostrar overlay de cargando
document.addEventListener("DOMContentLoaded", () => {
    Alpine.start();
    const notyf = new Notyf({
        duration: 2600,
        position: { x: 'right', y: 'top' }
    });

    // console.log("App Js loadead!");

    const overlay = document.getElementById('loading-overlay');

    // Ocultar el overlay con transición
    if (overlay) {
        const hideDelay = 1000;

        setTimeout(() => {
            overlay.classList.add('is-hidden');
        }, hideDelay);

        setTimeout(() => {
            overlay.remove();
        }, hideDelay + 450);
    }

    

    // Obtén la URL actual sin parámetros ni hash
    const currentPath = window.location.pathname.replace(/^\//, ''); // quita el slash inicial

    // Selecciona todos los enlaces del sidebar
    const links = document.querySelectorAll('.sidebar-link');

    // Llamado a la funcion de marcar el nav-link actual
    getActiveNav(currentPath, links);

    // Toggle de tema (modo noche)
    const themeSwitch = document.getElementById('theme-switch');

    if (themeSwitch) {
        const savedTheme = localStorage.getItem('theme-mode');

        if (savedTheme === 'dark') {
            document.body.classList.add('theme-dark');
            themeSwitch.checked = true;
        }

        themeSwitch.addEventListener('change', () => {
            document.body.classList.toggle('theme-dark', themeSwitch.checked);
            localStorage.setItem('theme-mode', themeSwitch.checked ? 'dark' : 'light');
        });
    }


    // Máscara para teléfono
    const phoneInputs = document.querySelectorAll('.mask-phone');

    phoneInputs.forEach(input => {
        IMask(input, {
            mask: '+{34} 000 000 000', // Ejemplo para España (+34)
        });
    });

    // Máscara para dinero (euros)
    const moneyInputs = document.querySelectorAll('.mask-money');

    moneyInputs.forEach(input => {
        IMask(input, {
            mask: Number,
            scale: 2, // Número de decimales
            signed: false, // No permite valores negativos
            thousandsSeparator: ',', // Separador de miles
            radix: '.', // Separador decimal
            mapToRadix: ['.'], // Permite usar "." como separador decimal
            padFractionalZeros: true, // Rellena con ceros los decimales
            normalizeZeros: true, // Normaliza los ceros al editar
            min: 0, // Valor mínimo
            max: 9999999999.99, // Valor máximo
            
        });
    });

    setupHeaderNotifications(notyf);

});

/**
 * Funcion encargada de marcar activo el nav-link segun la URL
 * @param {*} currentPath - Actual Path
 * @param {*} links - Modulos mostrados en el sidebar
 */
function getActiveNav(currentPath, links) {

    links.forEach(link => {

        link.classList.remove('active');

        let linkPath = link.getAttribute('url'); // ya no necesitas replace
        
        if (linkPath === currentPath || currentPath.startsWith(`${linkPath}/`)) {
            link.classList.add('active');
            localStorage.setItem('sidebar-active', linkPath);
        }

    });

}

function setupHeaderNotifications(notyf) {
    const dropdownButton = document.getElementById('notificationDropdown');
    const dropdownContainer = dropdownButton ? dropdownButton.closest('.dropdown') : null;
    const notificationsList = document.getElementById('headerNotificationList');
    const notificationsCount = document.getElementById('headerNotificationCount');
    const notificationsDot = document.getElementById('headerNotificationDot');
    const notificationsSubtitle = document.getElementById('headerNotificationSubtitle');
    const markAllButton = document.getElementById('markAllNotificationsBtn');

    if (!dropdownButton || !dropdownContainer || !notificationsList || !notificationsCount || !notificationsDot || !notificationsSubtitle || !markAllButton) {
        return;
    }

    const typeLabels = {
        stock_low: 'Stock Bajo',
        refund_created: 'Devoluciones',
    };

    const getTypeLabel = (type) => typeLabels[type] || type || 'General';

    const formatRelativeTime = (dateString) => {
        if (!dateString) {
            return 'Ahora';
        }

        const date = new Date(dateString);
        if (Number.isNaN(date.getTime())) {
            return 'Ahora';
        }

        const diffMs = Date.now() - date.getTime();
        const diffMinutes = Math.max(1, Math.floor(diffMs / 60000));

        if (diffMinutes < 60) {
            return `hace ${diffMinutes} min`;
        }

        const diffHours = Math.floor(diffMinutes / 60);
        if (diffHours < 24) {
            return `hace ${diffHours} h`;
        }

        const diffDays = Math.floor(diffHours / 24);
        return `hace ${diffDays} d`;
    };

    const renderNotifications = (items) => {
        if (!items.length) {
            notificationsList.innerHTML = `
                <div class="notification-empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <span>No hay notificaciones por ahora.</span>
                </div>
            `;
            return;
        }

        notificationsList.innerHTML = items.map((item) => {
            const notification = item.notification || {};
            const unreadClass = item.read_at ? '' : 'is-unread';

            return `
                <div class="notification-item ${unreadClass}" data-user-notification-id="${item.id}" data-read="${item.read_at ? '1' : '0'}">
                    <div class="notification-item-top">
                        <span class="notification-item-type">${getTypeLabel(notification.type)}</span>
                        <span class="notification-item-time">${formatRelativeTime(item.created_at || item.delivered_at || notification.created_at)}</span>
                    </div>
                    <div class="notification-item-title">${notification.title || 'Notificación'}</div>
                    <div class="notification-item-message">${notification.message || ''}</div>
                </div>
            `;
        }).join('');
    };

    const updateHeaderCounters = (items) => {
        const unreadCount = items.filter((item) => !item.read_at).length;
        const hasUnread = unreadCount > 0;

        notificationsDot.classList.toggle('d-none', !hasUnread);
        notificationsCount.classList.toggle('d-none', !hasUnread);
        notificationsCount.textContent = unreadCount > 99 ? '99+' : String(unreadCount);

        notificationsSubtitle.textContent = hasUnread
            ? `${unreadCount} sin leer`
            : 'Sin notificaciones nuevas';

        markAllButton.disabled = !hasUnread;
        markAllButton.classList.toggle('disabled', !hasUnread);
    };

    const loadNotifications = async () => {
        try {
            const response = await fetch('/notifications?per_page=8');
            if (!response.ok) {
                throw new Error('No se pudieron cargar notificaciones');
            }

            const payload = await response.json();
            const items = Array.isArray(payload.data) ? payload.data : [];
            renderNotifications(items);
            updateHeaderCounters(items);
        } catch (error) {
            notificationsList.innerHTML = `
                <div class="notification-empty-state">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Error al cargar notificaciones.</span>
                </div>
            `;
        }
    };

    const markOneAsRead = async (userNotificationId) => {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/notifications/${userNotificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
        });

        if (!response.ok) {
            throw new Error('No se pudo marcar la notificación');
        }
    };

    const markAllAsRead = async () => {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
        });

        if (!response.ok) {
            throw new Error('No se pudieron marcar todas');
        }
    };

    notificationsList.addEventListener('click', async (event) => {
        const item = event.target.closest('.notification-item');
        if (!item) {
            return;
        }

        if (item.dataset.read === '1') {
            return;
        }

        try {
            await markOneAsRead(item.dataset.userNotificationId);
            await loadNotifications();
        } catch (error) {
            notyf.error('No fue posible marcar la notificación');
        }
    });

    markAllButton.addEventListener('click', async () => {
        if (markAllButton.disabled) {
            return;
        }

        try {
            await markAllAsRead();
            await loadNotifications();
            notyf.success('Notificaciones marcadas como leídas');
        } catch (error) {
            notyf.error('No fue posible marcar todas');
        }
    });

    dropdownContainer.addEventListener('show.bs.dropdown', loadNotifications);
    loadNotifications();
}


/**
 * Función para validar un formulario
 * @param {HTMLFormElement} form - El formulario a validar
 * @returns {boolean} - Devuelve true si el formulario es válido, false si hay campos inválidos
 */
window.validateForm = function (form) {

    let isValid = true;

    // Selecciona todos los inputs, selects y textareas con la clase "form-control" dentro del formulario
    const inputs = form.querySelectorAll('.form-control');

    inputs.forEach(input => {

        if (input.value.trim() === '') {

            // Si el campo está vacío, agrega la clase "invalid-input" y remueve "valid-input"
            input.classList.add('invalid-input');
            input.classList.remove('valid-input');
            isValid = false;

        } else {

            // Si el campo tiene valor, agrega la clase "valid-input" y remueve "invalid-input"
            input.classList.add('valid-input');
            input.classList.remove('invalid-input');

        }

    });

    return isValid; // Devuelve true si todos los campos son válidos, false si hay algún campo inválido

}
