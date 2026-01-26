document.addEventListener("DOMContentLoaded", () => {

    console.log("Settings Js Loaded!");

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
}