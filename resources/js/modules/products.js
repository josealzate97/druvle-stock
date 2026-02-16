document.addEventListener("DOMContentLoaded", () => {

    console.log("Products Js Loaded!");

    const searchInput = document.getElementById('productsSearch');
    const categorySelect = document.getElementById('productsCategoryFilter');
    const table = document.querySelector('.section-table');

    if (searchInput && categorySelect && table) {
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const category = categorySelect.value;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                const matchesCategory = !category || row.dataset.category === category;
                row.style.display = matchesQuery && matchesCategory ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterRows);
        categorySelect.addEventListener('change', filterRows);
    }

    const productForm = document.getElementById('productForm');
    const productModalElement = document.getElementById('productModal');
    const productModal = new bootstrap.Modal(productModalElement);

    // Tax Switches & Selectors
    const taxSwitch = document.getElementById('productTaxSwitch');
    const taxDropdown = document.getElementById('productTax');
    const taxDropdownContainer = document.getElementById('taxDropdownContainer');

    // Campos que se usaran para la generacion del codigo de producto
    const nameInput = document.getElementById('productName');
    const codeInput = document.getElementById('productCode');

    taxSwitch.addEventListener('change', function() {
        
        if (this.checked) {
            taxDropdownContainer.style.display = 'block';
        } else {
            taxDropdownContainer.style.display = 'none';
            document.getElementById('productTax').value = '';
        }

    });

    productModalElement.addEventListener('show.bs.modal', function (event) {

        // Detecta el botón que disparó el modal
        const trigger = event.relatedTarget;
        const mode = trigger ? trigger.getAttribute('data-bs-mode') : null;

        if (mode === 'new') {
            clearProductModal();
        }
    });

    // Funcion para manejar el envio de datos, la salida de datos del formulario
    productForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (taxSwitch.checked && !taxDropdown.value) {
            
            event.preventDefault();
            notyf.error('Debes seleccionar un IVA si activas la opción "Aplica IVA".');
            return;

        }

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const url = data.id ? `/products/update/${data.id}` : '/products/create';

        try {

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {

                notyf.success(data.id ? 'Producto actualizado correctamente' : 'Producto creado correctamente');
                
                productModal.hide();

                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else {

                const errorData = await response.json();
                notyf.error(errorData.message || 'Error al guardar el producto');
            
            }

        } catch (error) {
            notyf.error('Error de red');
        }

    });



    // Funcion encargada de generar el codigo de producto apartir del nombre
    nameInput.addEventListener('input', function() {

        let name = nameInput.value.trim();

        if (name.length === 0) {
            codeInput.value = '';
            return;
        }

        // Toma las primeras 4 letras del nombre, en mayúsculas
        let prefix = name.replace(/\s+/g, '').substring(0, 4).toUpperCase();

        // Genera 4 números aleatorios
        let randomNum = Math.floor(1000 + Math.random() * 9000);
        codeInput.value = `${prefix}#${randomNum}`;

    });

    /**
     * Función para cargar datos en el modal
     * @param {*} productId
     * @returns {Promise<void>}
    */
    window.editProduct = async function (productId) {

        try {

            const response = await fetch(`/products/getProduct/${productId}`);
            const data = await response.json();

            if (data.success) {

                document.getElementById('productId').value = data.product.id;
                document.getElementById('productName').value = data.product.name;
                document.getElementById('productCode').value = data.product.code;
                document.getElementById('productCategory').value = data.product.category_id;
                document.getElementById('productPrice').value = data.product.purchase_price;
                document.getElementById('productSale').value = data.product.sale_price;
                document.getElementById('productQuantity').value = data.product.quantity;
                document.getElementById('productTaxSwitch').checked = data.product.taxable;
                document.getElementById('productNotes').value = data.product.notes || '';
                
                const taxDropdownContainer = document.getElementById('taxDropdownContainer');
                taxDropdownContainer.style.display = data.product.taxable ? 'block' : 'none';

                // Asigna el valor del IVA al dropdown
                document.getElementById('productTax').value = data.product.tax_id || '';

                const taxDropdown = document.getElementById('productTax');
                taxDropdown.value = data.product.tax_id || '';

                // Otros campos...
                productModal.show();

            } else {

                notyf.error('Error al cargar el producto');

            }

        } catch (error) {

            notyf.error('Error de red');

        }

    };

    /**
     * Funcion para eliminar un producto 
     * @param {*} productId
     * @returns {Promise<void>}
     */
    window.deleteProduct = async function (productId) {

        try {

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(`/products/delete/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });


            if (response.ok) {

                notyf.success('Producto desactivado correctamente');

                const badge = document.querySelector(`tr[data-id="${productId}"] .badge`);

                badge.textContent = 'Inactivo';
                badge.classList.remove('bg-success');
                badge.classList.add('bg-danger');

                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else {

                notyf.error('Error al desactivar el producto');
            }

        } catch (error) {

            notyf.error('Error de red');

        }

    };

    
    /**
     * Funcion encargada de activar un producto
     * @param {*} productId
     * @returns {Promise<void>}
    */
    window.activateProduct = async function (productId) {

        try {

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(`/products/activate/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });

            if (response.ok) {

                notyf.success('Producto activado correctamente');

                const badge = document.querySelector(`tr[data-id="${productId}"] .badge`);

                badge.textContent = 'Activo';
                badge.classList.remove('bg-danger');
                badge.classList.add('bg-success');

                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else {

                notyf.error('Error al activar el producto');

            }

        } catch (error) {

            notyf.error('Error de red');

        }

    };

    

});

const notyf = new Notyf();

/**
 * Función para limpiar el modal de producto
*/
function clearProductModal() {

    document.getElementById('productId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productCode').value = '';
    document.getElementById('productCategory').value = '';
    document.getElementById('productPrice').value = '';
    document.getElementById('productSale').value = '';
    document.getElementById('productQuantity').value = '';
    document.getElementById('productTaxSwitch').checked = false;
    document.getElementById('taxDropdownContainer').style.display = 'none';
    document.getElementById('productTax').value = '';
    document.getElementById('productNotes').value = '';

}
