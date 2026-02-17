@extends('backend.layouts.main')

@section('title', 'Ventas')

@push('styles')
    @vite(['resources/css/modules/sales.css'])
@endpush

@push('scripts')
    @vite(['resources/js/modules/sales.js'])
@endpush

@section('content')


    <div class="container-fluid p-4"  x-data="salesForm()" x-init="loadProducts()">

        @push('breadcrumb')
            @include('backend.components.breadcrumb', [
                'section' => [
                    'route' => 'sales.index',
                    'icon' => 'fas fa-shopping-cart',
                    'label' => 'Modulo Ventas'
                ]
            ])
        @endpush
        
        <div class="align-items-center border mb-4 bg-white rounded-3 p-4 col-12 d-flex sales-hero-card">

            <div class="col-md-6 col-12">
                <div class="d-flex align-items-center gap-3">
                    <span class="section-hero-icon">
                        <i class="fa fa-shopping-cart"></i>
                    </span>
                    <div>
                        <h3 class="fw-bold mb-0">Gestion de ventas</h3>
                        <div class="text-muted fw-bold small">Registra nuevas ventas o consulta el historial.</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-12 mt-3 mt-md-0 d-flex justify-content-md-end">
                
                <ul class="nav nav-pills bg-grey rounded p-2 gap-1" id="salesTabs" role="tablist">
                        
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="nueva-venta-tab" data-bs-toggle="pill" data-bs-target="#nueva-venta" type="button" role="tab" aria-controls="nueva-venta" aria-selected="true">
                            Nueva Venta
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="historial-tab" data-bs-toggle="pill" data-bs-target="#historial" type="button" role="tab" aria-controls="historial" aria-selected="false" @click="showModal = false">
                            Historial de Ventas
                        </button>
                    </li>
                        
                </ul>

            </div>

        </div>

        <div class="tab-content mt-4" id="salesTabsContent">

            <div class="tab-pane fade show active" id="nueva-venta" role="tabpanel" aria-labelledby="nueva-venta-tab">
                
                <!-- Contenido de Nueva Venta -->
                <div class="tab-pane fade show active" id="nueva-venta" role="tabpanel" aria-labelledby="nueva-venta-tab">

                    <div class="row g-4 sales-grid">

                        <div class="col-xl-8 col-lg-7">

                            <div class="card p-4 sales-card">

                                <!-- Cabecera -->
                                <div class="sales-card-header">
                                    <div>
                                        <h4 class="fw-bold mb-1">
                                            <i class="fas fa-plus-circle me-2 color-primary"></i>
                                            Crear Nueva Venta
                                        </h4>
                                        <p class="text-muted fw-bold small mb-0">Agrega tus productos de manera rápida y ordenada.</p>
                                    </div>

                                    <div class="sales-switch">
                                        <label for="facturaSwitch" class="form-label mb-0 fw-bold">¿Desea Factura?</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input custom-switch-success" 
                                            type="checkbox" id="facturaSwitch" name="factura" 
                                            x-model="showClientSection">
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <!-- Panel de accion para ventas -->
                                <div class="sales-action-card">

                                    <div class="sales-action-field">
                                        <label for="productSelect" class="form-label fw-bold">Producto</label>

                                        <!-- Campo de búsqueda de producto con sugerencias -->
                                        <div class="position-relative sales-input">
                                            <i class="fas fa-search"></i>
                                            <input
                                                type="text"
                                                id="productSearchInput"
                                                class="form-control"
                                                placeholder="Buscar producto por nombre"
                                                x-model="productSearch"
                                                :disabled="isProcessing"
                                                @focus="showProductDropdown = true"
                                                @input="showProductDropdown = true"
                                                @click.away="showProductDropdown = false"
                                            >

                                            <ul class="list-group position-absolute w-100 shadow-sm" 
                                                style="z-index: 1050; max-height: 250px; overflow-y: auto;"
                                                x-show="showProductDropdown && productSearch.length > 0"
                                                x-transition>
                                                
                                                <template x-for="product in products.filter(p => p.name.toLowerCase().includes(productSearch.toLowerCase()))" :key="product.id">
                                                    <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                                        style="cursor: pointer;"
                                                        @mousedown.prevent="
                                                            selectedProduct = product.id;
                                                            productSearch = product.name;
                                                            showProductDropdown = false;
                                                        ">
                                                        <span x-text="product.name"></span>
                                                        <span class="badge bg-secondary" x-text="'Stock: ' + product.quantity"></span>
                                                    </li>
                                                </template>

                                                <li class="list-group-item text-muted text-center" 
                                                    x-show="products.filter(p => p.name.toLowerCase().includes(productSearch.toLowerCase())).length === 0">
                                                    Sin resultados
                                                </li>
                                            </ul>
                                        </div>

                                    </div>

                                    <div class="sales-action-field small-field">
                                        <label for="quantityInput" class="form-label fw-bold">Cantidad</label>
                                        <input type="number" id="quantityInput" class="form-control" x-model="quantity" min="1" placeholder="Cantidad" :disabled="isProcessing">
                                    </div>

                                    <div class="sales-action-field small-field">
                                        <label for="salePriceInput" class="form-label fw-bold">Precio de Venta</label>
                                        <input type="number" id="salePriceInput" class="form-control" x-model="salePrice" min="0" step="0.01" placeholder="Precio de Venta" disabled>
                                    </div>

                                    <div class="sales-action-field small-field">
                                        <label for="taxSelect" class="form-label fw-bold">Impuesto</label>
                                        
                                        <select id="taxSelect" class="form-select" x-model="selectedTax" disabled>
                                            <option x-show="selectedProductObj && !selectedProductObj.tax" value="">NO</option>
                                            <template x-if="selectedProductObj && selectedProductObj.tax">
                                                <option :value="selectedProductObj.tax.id" x-text="selectedProductObj.tax.rate + ' %'"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="sales-action-field action-field">
                                        <button type="button" class="btn btn-warning mt-4 fw-bold w-100" @click="addProduct">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            Añadir
                                        </button>
                                    </div>

                                </div>

                                <!-- Seccion del cliente -->
                                <div class="row mt-4 sales-client-card" 
                                id="clientSection" x-show="showClientSection" x-transition x-cloak>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <label for="customerName" class="form-label fw-bold">Nombre del Cliente</label>
                                        <input type="text" id="customerName" class="form-control" x-model="customerName" placeholder="Nombre del Cliente" :disabled="isProcessing">
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <label for="customerEmail" class="form-label fw-bold">Email del Cliente</label>
                                        <input type="email" id="customerEmail" class="form-control" x-model="customerEmail" placeholder="Email del Cliente" :disabled="isProcessing">
                                    </div>
                                    
                                </div>

                            </div>

                            <div class="card p-0 mt-4 sales-summary">
                                <div class="sales-summary-header">Resumen de Venta</div>

                                <div class="table-responsive">

                                    <table class="table table-borderless align-middle section-table mb-0 sales-summary-table">

                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cant.</th>
                                                <th>Unitario</th>
                                                <th>Subtotal</th>
                                                <th>IVA</th>
                                                <th>Total</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <!-- Renderiza dinámicamente los productos en saleItems -->
                                            <template x-for="(item, index) in saleItems" :key="item.id">

                                                <tr>
                                                    <td x-text="item.name"></td>
                                                    <td x-text="item.quantity"></td>
                                                    <td x-text="Number(item.sale_price).toFixed(2) + ' €'"></td>
                                                    <td x-text="(item.quantity * Number(item.sale_price)).toFixed(2) + ' €'"></td>
                                                    <td x-text="(item.quantity * Number(item.tax_amount)).toFixed(2) + ' €'"></td>
                                                    <td x-text="(item.quantity * Number(item.sale_price) + (item.quantity * Number(item.tax_amount))).toFixed(2) + ' €'"></td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-icon text-danger" @click="removeProduct(index)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                            </template>

                                            <!-- Mensaje si no hay productos -->
                                            <tr x-show="saleItems.length === 0">
                                                <td colspan="7" class="text-center text-muted fw-bold fs-6">Aún no hay productos en la venta.</td>
                                            </tr>

                                        </tbody>

                                    </table>

                                </div>
                            </div>

                        </div>

                        <div class="col-xl-4 col-lg-5">

                            <div class="card p-4 payment-card">

                                <div class="payment-header">Detalle de Pago</div>

                                <div class="payment-row">
                                    <span>Subtotal</span>
                                    <strong x-text="salesHeaderData.subtotal > 0 ? salesHeaderData.subtotal + ' €' : '0.00 €'"></strong>
                                </div>

                                <div class="payment-row">
                                    <span>Impuestos (IVA)</span>
                                    <strong x-text="salesHeaderData.tax > 0 ? salesHeaderData.tax + ' €' : '0.00 €'"></strong>
                                </div>

                                <div class="payment-total">
                                    <span>Total a Pagar</span>
                                    <strong x-text="salesHeaderData.total > 0 ? salesHeaderData.total + ' €' : '0.00 €'"></strong>
                                </div>

                                <div class="payment-section">
                                    <label for="paymentType" class="form-label fw-bold mb-1">Método de pago</label>
                                    <select id="paymentType" class="form-select" x-model="paymentType">
                                        <option value="1">EFECTIVO</option>
                                        <option value="2">BIZUM</option>
                                        <option value="3">TPV</option>
                                    </select>
                                </div>

                                <div class="payment-highlight">
                                    <div>
                                        <small>Cambio sugerido</small>
                                        <strong>€0.00</strong>
                                    </div>
                                    <div>
                                        <small>Recibido</small>
                                        <strong>€0.00</strong>
                                    </div>
                                </div>

                                <button class="btn btn-success fw-bold btn-lg w-100"
                                :disabled="saleItems.length === 0 || isProcessing" @click="registerSale">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Registrar Venta
                                </button>

                                <button class="btn btn-outline-secondary fw-bold w-100 mt-2">
                                    <i class="fas fa-receipt me-2"></i>
                                    Solo Pre-ticket
                                </button>

                                <button class="btn btn-link text-danger fw-bold w-100 mt-2">Cancelar Venta Actual</button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="historial-tab">
                
                <!-- Historial de ventas -->
                @include('backend.sales.history', ['salesHistory' => $salesHistory])

            </div>

            

        </div>

        <!-- Modal para detalle de venta -->
        @include('backend.sales.detail')

    </div>

@endsection
