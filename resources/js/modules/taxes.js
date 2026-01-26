document.addEventListener("DOMContentLoaded", () => {

    console.log("Taxes Js Loaded!");

    // Botón para crear nuevo impuesto
    document.getElementById('btnNewTax').addEventListener('click', function() {
        newTax();
    });

    const taxForm = document.getElementById('taxForm');
    const taxModal = new bootstrap.Modal(document.getElementById('taxModal'));

    taxForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const isEdit = !!data.id;
        const url = isEdit ? `/taxes/update/${data.id}` : '/taxes/create';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Convertir status a booleano
        data.status = data.status === "1" ? true : false;

        try {

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {

                notyf.success(result.message);
                taxModal.hide();

                setTimeout(() => location.reload(), 1500);

            } else {

                notyf.error(result.message || 'Error al actualizar el IVA');
            }

        } catch (error) {
            notyf.error('Error de red');
        }

    });

    

    // Función para cargar datos en el modal
    window.editTax = function(tax) {

        document.getElementById('taxId').value = tax.id;
        document.getElementById('taxName').value = tax.name;
        document.getElementById('taxRate').value = tax.rate;
        document.getElementById('taxStatus').value = tax.status ? 1 : 0;
        document.getElementById('taxModalLabel').textContent = 'Editar impuesto';
        document.getElementById('taxStatus').disabled = false; // Habilita el campo estado
        
        taxModal.show();

    };

    // Función para abrir el modal en modo "nuevo"
    window.newTax = function() {

        document.getElementById('taxId').value = '';
        document.getElementById('taxName').value = '';
        document.getElementById('taxRate').value = '';
        document.getElementById('taxStatus').value = 1;
        document.getElementById('taxStatus').disabled = true; // Deshabilita el campo estado
        document.getElementById('taxModalLabel').textContent = 'Crear impuesto';

        taxModal.show();

    };

    // Función para activar un tax
    window.activateTax = async function(id) {

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {

            const response = await fetch(`/taxes/activate/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });

            const result = await response.json();

            if (result.success) {

                notyf.success(result.message);
                setTimeout(() => location.reload(), 1000);

            } else {

                notyf.error(result.message || 'Error al activar el IVA');
            }

        } catch (error) {
            notyf.error('Error de red');
        }

    };

    // Función para eliminar un tax
    window.deleteTax = async function(id) {

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {

            const response = await fetch(`/taxes/delete/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });

            const result = await response.json();

            if (result.success) {

                notyf.success(result.message);
                setTimeout(() => location.reload(), 1000);

            } else {

                notyf.error(result.message || 'Error al desactivar el IVA');

            }

        } catch (error) {
            notyf.error('Error de red');
        }
    };

});

const notyf = new Notyf();

