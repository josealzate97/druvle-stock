document.addEventListener("DOMContentLoaded", () => {
    console.log("Users Js Loaded!");

    const searchInput = document.getElementById('usersSearch');
    const roleSelect = document.getElementById('usersRoleFilter');
    const table = document.querySelector('.section-table');

    if (searchInput && roleSelect && table) {
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const role = roleSelect.value;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                const matchesRole = !role || row.dataset.role === role;
                row.style.display = matchesQuery && matchesRole ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterRows);
        roleSelect.addEventListener('change', filterRows);
    }
});

const notyf = new Notyf();

// Hacer que la función esté disponible globalmente
window.deleteUser = deleteUser;
window.activateUser = activateUser;

/**
 * Función para manejar el formulario de usuario
 * @param {Object} userData - Datos del usuario a editar
 * @returns {Object} - Objeto con métodos y propiedades para manejar el formulario
*/
window.userForm = function (userData) {
    
    return {
        editMode: false,
        isPasswordValid: true, // Estado inicial de la validación de la contraseña
        form: { ...userData, new_password: '' },
        toggleEdit() {
            this.editMode = !this.editMode;
        },
        async saveUser() {

            // const form = document.getElementsByClassName('form')[0];

            /*if (!validateForm(form)) {
                alert('Por favor, completa todos los campos obligatorios.');
                return false;
            }*/

            if (this.form.new_password.length > 0 && !this.isPasswordValid) {
                notyf.error('La contraseña no es válida.');
                return;
            }

            try {

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/users/update/${this.form.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.form)
                });

                // Si la respuesta es exitosa, actualizamos el estado
                // y mostramos un mensaje de éxito
                if (response.ok == true) {

                    this.editMode = false;

                    notyf.success('Usuario actualizado correctamente');

                } else {

                    notyf.error('Error al actualizar');

                }
                
            } catch (e) {

                notyf.error('Error de red');
                
            }
        },
        validatePassword() {

            if (this.form.new_password.length < 8) {

                this.isPasswordValid = false;
                notyf.error('La contraseña debe tener al menos 8 caracteres.');

            } else {

                this.isPasswordValid = true;
                notyf.success('La contraseña es válida.');

            }

        }
    }

}

/**
 * Función para eliminar un usuario
 * @param {number} userId - ID del usuario a eliminar
 * @returns {Promise<void>}
*/
async function deleteUser(userId) {

    try {

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch(`/users/delete/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });

        if (response.ok) {

            notyf.success('Usuario marcado como inactivo correctamente');

            const badge = document.querySelector(`tr[data-id="${userId}"] .badge`);
            
            badge.textContent = 'Inactivo';
            badge.classList.remove('bg-success');
            badge.classList.add('bg-danger');
            
            setTimeout(() => {
                location.reload();
            }, 3000);

        } else {

            notyf.error('Error al marcar como inactivo');

        }

    } catch (e) {
        notyf.error('Error de red');
    }
}

/** * Función para activar un usuario
 * @param {number} userId - ID del usuario a activar
 * @returns {Promise<void>}
*/
async function activateUser(userId) {

    try {

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch(`/users/activate/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });

        if (response.ok) {

            notyf.success('Usuario activado correctamente');
            
            // Cambia dinámicamente el badge del usuario
            const badge = document.querySelector(`tr[data-id="${userId}"] .badge`);

            badge.textContent = 'Activo';
            badge.classList.remove('bg-danger');
            badge.classList.add('bg-success');

            setTimeout(() => {
                location.reload();
            }, 3000);

        } else {

            notyf.error('Error al activar el usuario');

        }

    } catch (e) {

        notyf.error('Error de red');

    }
}
