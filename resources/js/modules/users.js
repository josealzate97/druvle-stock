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

            // Filtrar slides en móvil/tablet
            document.querySelectorAll('#userSlider .usr-slide').forEach(slide => {
                const text = slide.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                const matchesRole = !role || slide.dataset.role === role;
                slide.style.display = matchesQuery && matchesRole ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterRows);
        roleSelect.addEventListener('change', filterRows);
    }

    // ─── User Card Slider: dots con IntersectionObserver ───
    const usrSlider = document.getElementById('userSlider');
    const usrDotsContainer = document.getElementById('userSliderDots');

    if (usrSlider && usrDotsContainer) {
        const usrSlides = Array.from(usrSlider.querySelectorAll('.usr-slide'));
        const usrDots   = Array.from(usrDotsContainer.querySelectorAll('.usr-dot'));

        const setActiveDot = (index) => {
            usrDots.forEach((d, i) => d.classList.toggle('usr-dot--active', i === index));
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setActiveDot(usrSlides.indexOf(entry.target));
                }
            });
        }, { root: usrSlider, threshold: 0.6 });

        usrSlides.forEach(slide => observer.observe(slide));

        usrDots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                usrSlides[i].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            });
        });

        setActiveDot(0);
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
    let usernameValidationTimer = null;

    return {
        isCreateMode: userData.mode === 'create',
        editMode: userData.mode === 'create',
        isPasswordValid: userData.mode !== 'create',
        isEmailValid: userData.email ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(userData.email) : false,
        isUsernameValid: Boolean(String(userData.username || '').trim()),
        isCheckingUsername: false,
        lastValidatedUsername: String(userData.username || '').trim(),
        form: { ...userData, new_password: '' },
        init() {
            this.form.phone = this.formatColombianPhone(this.form.phone);
            this.validateEmail();
        },
        get normalizedPhone() {
            return (this.form.phone || '').replace(/\D/g, '');
        },
        get requiredFieldsComplete() {
            const hasBaseFields = [
                this.form.name,
                this.form.lastname,
                this.form.username,
                this.form.email,
            ].every((value) => String(value || '').trim().length > 0);

            return hasBaseFields && this.normalizedPhone.length === 10 && this.isEmailValid && this.isUsernameValid;
        },
        get isSubmitDisabled() {
            if (!this.editMode) {
                return true;
            }

             if (this.isCheckingUsername) {
                return true;
            }

            if (!this.requiredFieldsComplete) {
                return true;
            }

            if (this.isCreateMode) {
                return this.form.new_password.trim().length === 0 || !this.isPasswordValid;
            }

            return this.form.new_password.trim().length > 0 && !this.isPasswordValid;
        },
        toggleEdit() {
            if (this.isCreateMode) {
                return;
            }
            this.editMode = !this.editMode;
            if (!this.editMode) {
                this.form.new_password = '';
                this.isPasswordValid = true;
                this.isCheckingUsername = false;
            }
        },
        formatColombianPhone(value) {
            const digits = String(value || '').replace(/\D/g, '').slice(0, 10);

            if (digits.length <= 3) {
                return digits;
            }

            if (digits.length <= 6) {
                return `${digits.slice(0, 3)} ${digits.slice(3)}`;
            }

            return `${digits.slice(0, 3)} ${digits.slice(3, 6)} ${digits.slice(6)}`;
        },
        applyPhoneMask() {
            this.form.phone = this.formatColombianPhone(this.form.phone);
        },
        validateEmail() {
            const email = String(this.form.email || '').trim();
            this.isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        async runUsernameValidation(shouldNotify = false) {
            const username = String(this.form.username || '').trim();

            if (!username.length) {
                this.isUsernameValid = false;
                this.isCheckingUsername = false;
                return;
            }

            this.isCheckingUsername = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/users/check-username', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        username,
                        id: this.form.id || null,
                    })
                });

                const result = await response.json();
                this.isUsernameValid = Boolean(result.available);
                this.lastValidatedUsername = username;

                if (shouldNotify && !this.isUsernameValid) {
                    notyf.error(result.message || 'Este usuario ya existe para este negocio.');
                }
            } catch (e) {
                this.isUsernameValid = false;

                if (shouldNotify) {
                    notyf.error('No fue posible validar el usuario.');
                }
            } finally {
                this.isCheckingUsername = false;
            }
        },
        validateUsername(shouldNotify = false) {
            const username = String(this.form.username || '').trim();

            if (usernameValidationTimer) {
                clearTimeout(usernameValidationTimer);
            }

            if (!username.length) {
                this.isUsernameValid = false;
                this.isCheckingUsername = false;
                return;
            }

            if (username === this.lastValidatedUsername && this.isUsernameValid) {
                this.isCheckingUsername = false;
                return;
            }

            if (shouldNotify) {
                this.runUsernameValidation(true);
                return;
            }

            this.isCheckingUsername = true;
            usernameValidationTimer = setTimeout(() => {
                this.runUsernameValidation(false);
            }, 350);
        },
        async saveUser() {

            // const form = document.getElementsByClassName('form')[0];

            /*if (!validateForm(form)) {
                alert('Por favor, completa todos los campos obligatorios.');
                return false;
            }*/

            if (!this.requiredFieldsComplete) {
                notyf.error('Completa todos los campos obligatorios.');
                return;
            }

            if (this.isCheckingUsername) {
                notyf.error('Espera a que termine la validación del usuario.');
                return;
            }

            if (!this.isUsernameValid) {
                notyf.error('El usuario no está disponible para este negocio.');
                return;
            }

            if ((this.isCreateMode && this.form.new_password.length === 0) || (this.form.new_password.length > 0 && !this.isPasswordValid)) {
                notyf.error('La contraseña no es válida.');
                return;
            }

            try {

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const endpoint = this.isCreateMode ? '/users/create' : `/users/update/${this.form.id}`;
                const payload = {
                    ...this.form,
                    phone: this.normalizedPhone,
                };

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                });

                // Si la respuesta es exitosa, actualizamos el estado
                // y mostramos un mensaje de éxito
                if (response.ok == true) {

                    if (this.isCreateMode) {
                        notyf.success('Usuario creado correctamente');
                        window.setTimeout(() => {
                            window.location.href = '/users';
                        }, 1200);
                    } else {
                        this.editMode = false;
                        notyf.success('Usuario actualizado correctamente');
                    }

                } else {

                    const result = await response.json().catch(() => ({}));
                    notyf.error(result.message || 'Error al actualizar');

                }
                
            } catch (e) {

                notyf.error('Error de red');
                
            }
        },
        validatePassword(shouldNotify = false) {

            if (!this.form.new_password.length) {
                this.isPasswordValid = !this.isCreateMode;
                return;
            }

            if (this.form.new_password.length < 8) {

                this.isPasswordValid = false;
                if (shouldNotify) {
                    notyf.error('La contraseña debe tener al menos 8 caracteres.');
                }

            } else {

                this.isPasswordValid = true;
                if (shouldNotify) {
                    notyf.success('La contraseña es válida.');
                }

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
