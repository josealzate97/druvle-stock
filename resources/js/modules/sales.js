document.addEventListener("DOMContentLoaded", () => {

    console.log("Sales Js Loaded!");

    const searchInput = document.getElementById('salesHistorySearch');
    const statusSelect = document.getElementById('salesHistoryStatus');
    const table = document.getElementById('salesHistoryTable');

    if (searchInput && statusSelect && table) {
        const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => !row.classList.contains('sales-history-empty'));
        const emptyRow = table.querySelector('.sales-history-empty');

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const status = statusSelect.value;
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                const matchesStatus = !status || row.dataset.status === status;

                const show = matchesQuery && matchesStatus;
                row.style.display = show ? '' : 'none';
                if (show) {
                    visibleCount += 1;
                }
            });

            if (emptyRow) {
                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        };

        searchInput.addEventListener('input', filterRows);
        statusSelect.addEventListener('change', filterRows);
    }

    const url = new URL(window.location.href);
    const tab = url.searchParams.get('tab');
    if (tab === 'historial') {
        const historyTab = document.getElementById('historial-tab');
        if (historyTab && window.bootstrap?.Tab) {
            const tabInstance = new window.bootstrap.Tab(historyTab);
            tabInstance.show();
        }
    }
    
});

const notyf = new Notyf(
    {
        types: [
            {
                type: 'info',
                background: '#2196f3', // azul visible
                icon: {
                    className: 'fas fa-info-circle',
                    tagName: 'i',
                    color: '#fff'
                }
            }
        ]
    }
);

window.salesForm = function() {

    return {
        products: [],
        productSearch: '',
        showProductDropdown: false,
        selectedProduct: '',
        selectedProductObj: null,
        salePrice: '',
        selectedTax: '',
        quantity: 1,
        saleItems: [], // Arreglo donde se guardarán los productos seleccionados
        salesHeaderData: {}, // Nuevo objeto para almacenar los datos del encabezado
        saleDetail: {
            items: []
        },
        baseAmount: 0,
        taxAmount: 0,
        totalAmount: 0,
        paymentType: 1,
        customerName: '',
        customerEmail: '',
        showClientSection: false, // Controla la visibilidad de la sección del cliente
        saleId: null, // ID de la venta para el modal
        isProcessing: false,

        // Carga los productos activos al iniciar
        async loadProducts() {

            try {

                const response = await fetch('/products/getActiveProducts');
                const rawProducts = await response.json();

                 if (Array.isArray(rawProducts)) {

                    this.products = rawProducts.filter((product, index, self) =>
                        index === self.findIndex((p) => p.id === product.id)
                    );

                } else if (rawProducts.data && Array.isArray(rawProducts.data)) {

                    this.products = rawProducts.data.filter((product, index, self) =>
                        index === self.findIndex((p) => p.id === product.id)
                    );

                } else {

                    console.error('Formato de respuesta no válido:', rawProducts);

                }

            } catch (error) {
                console.error('Error al cargar productos:', error);
            }

        },

        // Método para obtener el nombre del producto por ID
        getProductNameBySaleDetailId(id) {
            const item = this.saleDetail.items.find(i => i.id === id);
            return item ? item.name : 'Producto eliminado';
        },

        // Método para agregar un producto al carrito de ventas
        addProduct() {

            const product = this.products.find(p => p.id === this.selectedProductObj.id);

            if (!product || this.quantity <= 0) {

                alert('Seleccione un producto válido y una cantidad mayor a 0.');
                return;

            } else if (this.quantity > product.quantity) {

                notyf.error(`Cantidad no disponible. Stock actual: ${product.quantity}`);
                return;

            }

            // Verificar si el producto ya existe en saleItems
            const existingItem = this.saleItems.find(item => item.id === product.id);

            if (existingItem) {

                // Si el producto ya existe, sumar la cantidad
                existingItem.quantity += parseInt(this.quantity);
                notyf.success('Cantidad actualizada correctamente');

            } else {

                // Si el producto no existe, agregarlo al array
                this.saleItems.push({
                    id: product.id,
                    name: product.name,
                    quantity: parseInt(this.quantity),
                    sale_price: Number(product.sale_price),
                    purchase_price: Number(product.purchase_price),
                    tax: product.tax ? product.tax.id : null,
                    tax_rate: product.tax ? product.tax.rate : null,
                    tax_name: product.tax ? product.tax.name : null,
                    tax_amount: product.tax ? (Number(product.sale_price) * product.tax.rate / 100) : 0,
                });

                notyf.success('Producto agregado correctamente');
            }

            // Recalcular los totales
            this.calculateTotals();

            // Limpiar los campos después de agregar el producto
            this.selectedProduct = '';
            this.quantity = 1;

        },

        // Método para eliminar un producto del carrito de ventas
        removeProduct(index) {

            this.saleItems.splice(index, 1); // Elimina el producto del array

            notyf.success('Producto eliminado correctamente');
            this.calculateTotals(); // Recalcula los totales

        },

        // Método para mostrar u ocultar la sección del cliente
        toggleClientSection() {
            this.showClientSection = !this.showClientSection;
        },

        // Método para calcular los totales
        calculateTotals() {

            this.baseAmount = this.saleItems.reduce((sum, item) => sum + item.quantity * item.sale_price, 0);
            this.taxAmount = this.saleItems.reduce((sum, item) => sum + item.quantity * item.tax_amount, 0);
            this.totalAmount = this.baseAmount + this.taxAmount;

            // Actualiza salesHeaderData
            this.salesHeaderData = {
                subtotal: this.baseAmount.toFixed(2),
                tax: this.taxAmount.toFixed(2),
                total: this.totalAmount.toFixed(2),
            };

        },

        // Método para registrar una nueva venta
        async registerSale() {

            try {

                this.isProcessing = true;

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Validar que haya al menos un producto en saleItems
                if (this.saleItems.length === 0) {
                    notyf.error('Debe agregar al menos un producto a la venta.');
                    this.isProcessing = false;
                    return;
                }

                // Validamos si desean factura para el cliente y si hay datos del cliente
                if (this.showClientSection && (!this.customerName || !this.customerEmail)) {

                    notyf.error('Debe ingresar el nombre y correo del cliente.');
                    this.isProcessing = false;
                    return;

                } else {

                    // Si hay datos del cliente, los agregamos a salesHeaderData
                    this.salesHeaderData.client_name = this.customerName;
                    this.salesHeaderData.client_email = this.customerEmail;

                }

                // asignamos el tipo de pago
                this.salesHeaderData.payment_type = this.paymentType;

                // Mostrar mensaje si hay correo
                if (this.salesHeaderData.client_email) {
                    notyf.open({
                        type: 'info',
                        message: 'Enviando correo al cliente...',
                        duration: 5000
                    });
                }


                const response = await fetch('/sales/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({
                        salesHeaderData: this.salesHeaderData,
                        saleItems: this.saleItems,
                    }),
                });

                if (response.ok) {

                    notyf.success("Venta registrada correctamente");
                    
                    setTimeout(() => {
                        location.reload();
                    }, 3000);

                } else {
                    notyf.error(response.message || 'Error al registrar la venta.');
                }

            } catch (error) {

                console.error('Error al registrar la venta:', error);
                notyf.error('Error de red.');

            }

        },

        // Método para abrir el modal detalle de la venta
        openSaleModal(id) {

            this.saleId = id;

            this.saleDetail = { items: [] };

            // Mostrar el modal usando Bootstrap JS
            const modal = new bootstrap.Modal(document.getElementById('saleDetailModal'));
            modal.show();

            // Cargar los detalles de la venta
            fetch(`/sales/detail/${id}`).then(res => res.json()).then(data => {
               
                if (data && Array.isArray(data.items)) {
                    this.saleDetail = data;
                } else {
                    console.error('Formato de datos inválido:', data);
                    this.saleDetail = { items: [] };
                }

            }).catch(() => {

                this.saleDetail = { code: 'Error', items: [] };

            });

        },

        // Método para imprimir la factura
        printInvoice(id) {

            // Lógica para imprimir
            window.open(`/sales/invoice/${id}`, '_blank');
        },

        // Método para devolver productos de una venta
        returnProducts(id) {

            // Oculta el modal de detalle
            const detailModal = bootstrap.Modal.getInstance(document.getElementById('saleDetailModal'));
            if (detailModal) detailModal.hide();

            // Cargar el formulario de devolución por AJAX y mostrar en un modal
            fetch(`/sales/refund-form/${id}`).then(res => res.text()).then(html => {

                const content = document.getElementById('refundModalContent');

                if (content) {

                    content.innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('refundModal'));
                    
                    modal.show();

                    // --- AQUÍ VAN LOS EVENTOS DINÁMICOS ---

                    // Botón volver al detalle
                    const backBtn = document.getElementById('backToDetailBtn');
                    const processBtn = document.getElementById('processRefundBtn');

                    if (backBtn) {

                        backBtn.onclick = function() {

                            const refundModal = bootstrap.Modal.getInstance(document.getElementById('refundModal'));
                            
                            if (refundModal) refundModal.hide();

                            let detailModal = bootstrap.Modal.getInstance(document.getElementById('saleDetailModal'));
                            
                            if (!detailModal) {
                                detailModal = new bootstrap.Modal(document.getElementById('saleDetailModal'));
                            }

                            detailModal.show();
                        };

                    }

                    if (processBtn) {

                        processBtn.onclick = () => {

                            // Aquí va la lógica para recolectar los datos y enviarlos
                            const form = document.getElementById('returnForm');
                            const formData = new FormData(form);

                            let items = [];
                            let saleId = document.querySelectorAll('input[name^="sale_id"]')[0].value;

                            document.querySelectorAll('input[name^="return_quantity"]').forEach(input => {
                                
                                let qty = parseInt(input.value);
                                let productId = input.getAttribute('data-product-id');
                                let sale = input.getAttribute('data-sale-id');
                                let saleDetailId = input.getAttribute('data-sale-detail-id');
                                let note = document.querySelector(`textarea[name="return_note[${saleDetailId}]"]`).value;
                                let reason = document.querySelector(`select[name="return_reason[${saleDetailId}]"]`).value;

                                if (qty > 0) {
                                    items.push({
                                        product_id: productId,
                                        quantity: qty,
                                        sale_id: sale,
                                        sale_detail_id: saleDetailId,
                                        note: note || null,
                                        reason: reason || null
                                    });
                                }

                            });

                            fetch(`/sales/refund/${saleId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ items: items, sale_id: saleId })
                            }).then(res => res.json()).then(data => {

                                if (data.success) {

                                    notyf.success('Devolución procesada correctamente');
                                    location.reload();

                                } else {
                                    notyf.error('Error al procesar devolución');
                                }

                            });

                        };

                    }

                    // Inputs de cantidad a devolver
                    document.querySelectorAll('input[name^="return_quantity"]').forEach(input => {
                        input.addEventListener('input', updateTotalRefund);
                    });

                    // Función para actualizar el total
                    function updateTotalRefund() {

                        let total = 0;

                        document.querySelectorAll('input[name^="return_quantity"]').forEach(input => {
                            
                            const qty = parseInt(input.value) || 0;
                            const price = parseFloat(input.dataset.price) || 0;

                            total += qty * price;

                        });

                        const totalRefund = document.getElementById('totalRefund');
                        if (totalRefund) totalRefund.innerText = total.toFixed(2) + ' €';
                        
                    }

                } else {
                    console.error('refundModalContent no existe en el DOM');
                }
            });

        },

        // Observa el cambio de producto seleccionado
        async init() {

            this.$watch('selectedProduct', value => {

                this.selectedProductObj = this.products.find(p => p.id === value) || null;
                this.salePrice = this.selectedProductObj ? this.selectedProductObj.sale_price : '';

                this.selectedTax = this.selectedProductObj && this.selectedProductObj.tax ? this.selectedProductObj.tax.id : '';

            });

        },

    };
}
