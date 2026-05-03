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
    const productDetailsModalElement = document.getElementById('productDetailsModal');
    const productDetailsModal = productDetailsModalElement ? new bootstrap.Modal(productDetailsModalElement) : null;

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

            let payload = null;

            try {
                payload = await response.json();
            } catch (_) {}

            if (response.ok && payload?.success === true) {

                notyf.success(payload.message || (data.id ? 'Producto actualizado correctamente' : 'Producto creado correctamente'));

                productModal.hide();

                setTimeout(() => {
                    location.reload();
                }, 1200);

            } else {

                let message = payload?.message || `Error ${response.status} al guardar el producto`;

                if (!payload?.message) {
                    try {
                        const raw = await response.text();
                        if (raw) {
                            message = raw.substring(0, 220);
                        }
                    } catch (__) {}
                }

                notyf.error(message);
            }

        } catch (error) {
            notyf.error('Error de red o servidor no disponible');
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

    window.showProductDetails = async function (productId) {
        if (!productDetailsModal) return;

        try {
            const response = await fetch(`/products/getProductDetails/${productId}`);
            const data = await response.json();

            if (!response.ok || !data.success) {
                notyf.error(data.message || 'No se pudo cargar el detalle del producto');
                return;
            }

            const product = data.product;

            document.getElementById('productDetailsName').textContent = product.name || '-';
            document.getElementById('productDetailsCode').textContent = product.code || '-';
            document.getElementById('productDetailsCategory').textContent = product.category?.name || '-';
            document.getElementById('productDetailsStatus').textContent = Number(product.status) === 1 ? 'Activo' : 'Inactivo';
            document.getElementById('productDetailsTax').textContent = product.tax?.rate ? `${product.tax.rate} %` : 'No aplica';
            document.getElementById('productDetailsType').textContent = product.has_sizes ? 'Con tallas' : 'Sin tallas';

            const notes = (product.notes || '').trim();
            document.getElementById('productDetailsNotes').textContent = notes || 'Sin notas';

            const baseStockBlock = document.getElementById('productDetailsBaseStockBlock');
            const priceQty = document.getElementById('productDetailsPriceQty');
            const sizesSection = document.getElementById('productDetailsSizesSection');
            const sizesBody = document.getElementById('productDetailsSizesBody');

            if (product.has_sizes) {
                baseStockBlock.style.display = 'none';
                sizesSection.style.display = 'block';

                const sizes = Array.isArray(product.sizes) ? product.sizes : [];
                sizesBody.innerHTML = sizes.length
                    ? sizes.map((size) => `
                        <tr>
                            <td>${escapeHtml(size.name || '-')}</td>
                            <td>$ ${formatCopMoneyInput(size.price ?? 0)}</td>
                            <td>${Number(size.quantity || 0)}</td>
                            <td>${Number(size.status) === 1 ? 'Activa' : 'Inactiva'}</td>
                        </tr>
                    `).join('')
                    : '<tr><td colspan="4" class="text-muted">Sin tallas registradas.</td></tr>';
            } else {
                baseStockBlock.style.display = '';
                sizesSection.style.display = 'none';
                sizesBody.innerHTML = '';
                priceQty.textContent = `$ ${formatCopMoneyInput(product.sale_price ?? 0)} / ${Number(product.quantity ?? 0)} und`;
            }

            productDetailsModal.show();
        } catch (error) {
            notyf.error('Error al cargar el detalle del producto');
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
            <input type="text" class="form-control size-price mask-money" placeholder="0,00" value="${escapeHtml(formatCopMoneyInput(size.price ?? ''))}">
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
    const priceInput = row.querySelector('.size-price');
    applyMoneyMask(priceInput);

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

        const price = parseLocaleDecimal(priceRaw);
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

function parseLocaleDecimal(value) {
    const raw = String(value || '').trim().replace(/\s+/g, '').replace(/\$/g, '').replace(/COP/gi, '');
    if (!raw) return NaN;

    const commaPos = raw.lastIndexOf(',');
    const dotPos = raw.lastIndexOf('.');
    let normalized = raw;

    if (commaPos !== -1 && dotPos !== -1) {
        if (commaPos > dotPos) {
            normalized = raw.replace(/\./g, '').replace(',', '.');
        } else {
            normalized = raw.replace(/,/g, '');
        }
    } else if (commaPos !== -1) {
        normalized = raw.replace(',', '.');
    }

    return parseFloat(normalized);
}

function formatCopMoneyInput(value) {
    const amount = parseLocaleDecimal(value);
    if (Number.isNaN(amount)) return '';

    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
}

function applyMoneyMask(input) {
    if (!input || !window.IMask) return;

    window.IMask(input, {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: '.',
        radix: ',',
        mapToRadix: ['.'],
        padFractionalZeros: true,
        normalizeZeros: true,
        min: 0,
    });
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
