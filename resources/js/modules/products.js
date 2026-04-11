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

    const taxSwitch = document.getElementById('productTaxSwitch');
    const taxDropdown = document.getElementById('productTax');
    const taxDropdownContainer = document.getElementById('taxDropdownContainer');

    const nameInput = document.getElementById('productName');
    const codeInput = document.getElementById('productCode');

    const addSizeRowBtn = document.getElementById('addSizeRowBtn');
    const hasSizesSwitch = document.getElementById('productHasSizesSwitch');
    const sizesSection = document.getElementById('sizesSection');

    taxSwitch.addEventListener('change', function() {
        if (this.checked) {
            taxDropdownContainer.style.display = 'block';
        } else {
            taxDropdownContainer.style.display = 'none';
            taxDropdown.value = '';
        }
    });

    if (addSizeRowBtn) {
        addSizeRowBtn.addEventListener('click', () => addSizeRow());
    }

    if (hasSizesSwitch) {
        hasSizesSwitch.addEventListener('change', function () {
            toggleSizesSection(this.checked);
            toggleBaseStockFields(this.checked);
        });
    }

    productModalElement.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const mode = trigger ? trigger.getAttribute('data-bs-mode') : null;

        if (mode === 'new') {
            clearProductModal();
        }
    });

    productForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (taxSwitch.checked && !taxDropdown.value) {
            notyf.error('Debes seleccionar un IVA si activas la opción "Aplica IVA".');
            return;
        }

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const hasSizes = hasSizesSwitch ? hasSizesSwitch.checked : false;
        data.has_sizes = hasSizes;

        if (hasSizes) {
            const sizesPayload = collectSizesPayload();
            if (sizesPayload.error) {
                notyf.error(sizesPayload.error);
                return;
            }

            if (sizesPayload.sizes.length === 0) {
                notyf.error('Debes agregar al menos una talla.');
                return;
            }

            data.sizes = sizesPayload.sizes;
        } else {
            data.sizes = [];
        }

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

    nameInput.addEventListener('input', function() {

        let name = nameInput.value.trim();

        if (name.length === 0) {
            codeInput.value = '';
            return;
        }

        let prefix = name.replace(/\s+/g, '').substring(0, 4).toUpperCase();
        let randomNum = Math.floor(1000 + Math.random() * 9000);
        codeInput.value = `${prefix}#${randomNum}`;
    });

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
                document.getElementById('productHasSizesSwitch').checked = Boolean(Number(data.product.has_sizes));
                document.getElementById('productTaxSwitch').checked = data.product.taxable;
                document.getElementById('productNotes').value = data.product.notes || '';

                taxDropdownContainer.style.display = data.product.taxable ? 'block' : 'none';
                taxDropdown.value = data.product.tax_id || '';
                const hasSizes = Boolean(data.product.has_sizes);
                toggleSizesSection(hasSizes);
                toggleBaseStockFields(hasSizes);
                renderSizeRows(Array.isArray(data.product.sizes) ? data.product.sizes : []);

                productModal.show();
            } else {
                notyf.error('Error al cargar el producto');
            }

        } catch (error) {
            notyf.error('Error de red');
        }
    };

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

    toggleSizesSection(false);
    toggleBaseStockFields(false);
    renderSizeRows([]);
});

const notyf = new Notyf();

function clearProductModal() {

    document.getElementById('productId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productCode').value = '';
    document.getElementById('productCategory').value = '';
    document.getElementById('productPrice').value = '';
    document.getElementById('productSale').value = '';
    document.getElementById('productQuantity').value = '';
    document.getElementById('productHasSizesSwitch').checked = false;
    document.getElementById('productTaxSwitch').checked = false;
    document.getElementById('taxDropdownContainer').style.display = 'none';
    document.getElementById('productTax').value = '';
    document.getElementById('productNotes').value = '';

    toggleSizesSection(false);
    toggleBaseStockFields(false);
    renderSizeRows([]);
}

function renderSizeRows(sizes = []) {
    const container = document.getElementById('sizeRowsContainer');
    if (!container) return;

    container.innerHTML = '';

    if (!Array.isArray(sizes) || sizes.length === 0) {
        addSizeRow();
        return;
    }

    sizes.forEach(size => addSizeRow(size));
}

function addSizeRow(size = {}) {
    const container = document.getElementById('sizeRowsContainer');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end size-row border rounded p-2';

    const isActive = typeof size.status === 'undefined' ? true : Boolean(Number(size.status));

    row.innerHTML = `
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label">Talla</label>
            <input type="text" class="form-control size-name" maxlength="20" placeholder="Ej: S, M, L" value="${escapeHtml(size.name || '')}">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" min="0" class="form-control size-price" placeholder="0.00" value="${escapeHtml(size.price ?? '')}">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label">Cantidad</label>
            <input type="number" min="0" class="form-control size-quantity" placeholder="0" value="${escapeHtml(size.quantity ?? '')}">
        </div>
        <div class="col-lg-2 col-md-4 col-sm-8">
            <label class="form-label d-block">Estado</label>
            <div class="form-check form-switch mt-2">
                <input class="form-check-input size-status" type="checkbox" ${isActive ? 'checked' : ''}>
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-4 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger size-remove-btn">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    const removeBtn = row.querySelector('.size-remove-btn');
    removeBtn.addEventListener('click', () => {
        const rows = container.querySelectorAll('.size-row');

        if (rows.length <= 1) {
            row.querySelector('.size-name').value = '';
            row.querySelector('.size-price').value = '';
            row.querySelector('.size-quantity').value = '';
            row.querySelector('.size-status').checked = true;
            return;
        }

        row.remove();
    });

    container.appendChild(row);
}

function toggleSizesSection(show) {
    const sizesSection = document.getElementById('sizesSection');
    if (!sizesSection) return;

    sizesSection.style.display = show ? 'block' : 'none';
}

function toggleBaseStockFields(hasSizes) {
    const purchasePriceInput = document.getElementById('productPrice');
    const salePriceInput = document.getElementById('productSale');
    const quantityInput = document.getElementById('productQuantity');

    const required = !hasSizes;

    [purchasePriceInput, salePriceInput, quantityInput].forEach(input => {
        if (!input) return;
        input.required = required;
        input.disabled = hasSizes;
    });
}

function collectSizesPayload() {
    const rows = Array.from(document.querySelectorAll('#sizeRowsContainer .size-row'));
    const sizes = [];

    for (const row of rows) {
        const name = row.querySelector('.size-name').value.trim();
        const priceRaw = row.querySelector('.size-price').value.trim();
        const quantityRaw = row.querySelector('.size-quantity').value.trim();
        const status = row.querySelector('.size-status').checked;

        const isEmpty = !name && !priceRaw && !quantityRaw;
        if (isEmpty) {
            continue;
        }

        if (!name || priceRaw === '' || quantityRaw === '') {
            return { error: 'Completa nombre, precio y cantidad en cada talla.' };
        }

        const price = parseFloat(priceRaw.replace(',', '.'));
        const quantity = parseInt(quantityRaw, 10);

        if (Number.isNaN(price) || price < 0) {
            return { error: 'El precio de talla debe ser un número válido mayor o igual a 0.' };
        }

        if (Number.isNaN(quantity) || quantity < 0) {
            return { error: 'La cantidad de talla debe ser un número entero mayor o igual a 0.' };
        }

        sizes.push({
            name,
            price,
            quantity,
            status,
        });
    }

    return { sizes };
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
