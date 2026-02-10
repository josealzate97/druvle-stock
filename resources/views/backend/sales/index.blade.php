@extends('backend.layouts.main')

@section('title', 'Ventas')

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
        
        <div class="align-items-center border mb-4 bg-white rounded-3 p-4 col-12 d-flex">

            <div class="col-md-6 col-12">
                <h3 class="fw-bold mb-0">
                    <i class="fa fa-shopping-cart me-2 color-primary"></i>
                    Gestion de ventas
                </h3>
                <div class="text-muted fw-bold small">Registra nuevas ventas o consulta el historial.</div>
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

                    <div class="card p-4">

                        <!-- Cabecera -->
                        <div class="col-lg-12 col-md-12 col-sm-12 d-flex align-items-center justify-content-between">
                            
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <h4 class="fw-bold">
                                    <i class="fas fa-plus-circle me-2 color-primary"></i>
                                    Crear Nueva Venta
                                </h4>
                                <p class="text-muted fw-bold small">Agrega tus Productos de manera facil y rapida</p>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column align-items-center justify-content-center">
                                <label for="facturaSwitch" class="form-label mb-0 fw-bold">¿Desea Factura?</label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input custom-switch-success" 
                                    type="checkbox" id="facturaSwitch" name="factura" 
                                    x-model="showClientSection">
                                </div>
                            </div>

                        </div>

                        <hr>

                        <!-- Panel de accion para ventas -->
                        <div class="col-lg-12 col-md-12 col-sm-12 bg-grey d-flex align-items-center rounded-3 p-3 mb-4 gap-2">

                            <div class="col-md-4">

                                <label for="productSelect" class="form-label fw-bold">Producto</label>

                                <!-- Select original comentado para mantener referencia sin usar -->
                                <!--
                                <select id="productSelect" class="form-select" x-model="selectedProduct" :disabled="isProcessing">
                                    <option value="" disabled selected>Seleccione un producto</option>
                                    <template x-for="product in products" :key="product.id">
                                        <option :value="product.id" x-text="product.name + ' - '+' Stock: '+ product.quantity"></option>
                                    </template>
                                </select>
                                -->

                                <!-- Campo de búsqueda de producto con sugerencias -->
                                <div class="position-relative">
                                    <input
                                        type="text"
                                        id="productSearchInput"
                                        class="form-control"
                                        placeholder="Buscar producto..."
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

                            <div class="col-md-2">
                                <label for="quantityInput" class="form-label fw-bold">Cantidad</label>
                                <input type="number" id="quantityInput" class="form-control" x-model="quantity" min="1" placeholder="Cantidad" :disabled="isProcessing">
                            </div>

                            <div class="col-md-2">
                                <label for="salePriceInput" class="form-label fw-bold">Precio de Venta</label>
                                <input type="number" id="salePriceInput" class="form-control" x-model="salePrice" min="0" step="0.01" placeholder="Precio de Venta" disabled>
                            </div>

                            <div class="col-md-2">

                                <label for="taxSelect" class="form-label fw-bold">Impuesto</label>
                                
                                <select id="taxSelect" class="form-select" x-model="selectedTax" disabled>
                                    
                                    <option x-show="selectedProductObj && !selectedProductObj.tax" value="">NO</option>
                                    <template x-if="selectedProductObj && selectedProductObj.tax">
                                        <option :value="selectedProductObj.tax.id" x-text="selectedProductObj.tax.rate + ' %'"></option>
                                    </template>

                                </select>

                            </div>


                            <div class="col-md-2 mt-3">
                                <button type="button" class="btn btn-warning fw-bold col-8 mt-3" @click="addProduct">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Añadir
                                </button>
                            </div>

                        </div>

                        
                        <!-- resumen de venta -->
                        <div class="mb-2 fw-bold fs-3">Resumen de Venta</div>

                        <div class="table-responsive mb-4">

                            <table class="table table-borderless align-middle table-striped">

                                <thead>
                                    <tr class="text-success">
                                        <th class="fw-bold color-primary">Producto</th>
                                        <th class="fw-bold color-primary">Cantidad</th>
                                        <th class="fw-bold color-primary">Precio Unit</th>
                                        <th class="fw-bold color-primary">Subtotal</th>
                                        <th class="fw-bold color-primary">IVA</th>
                                        <th class="fw-bold color-primary">Total</th>
                                        <th class="fw-bold color-primary text-center">Acciones</th>
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
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" @click="removeProduct(index)">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </td>
                                        </tr>

                                    </template>

                                    <!-- Mensaje si no hay productos -->
                                    <tr x-show="saleItems.length === 0">
                                        <td colspan="7" class="text-center text-muted fw-bold fs-5">Aún no hay productos en la venta.</td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <!-- Seccion del cliente -->
                        <div class="row my-3 bg-light rounded p-4 mb-4" 
                        id="clientSection" x-show="showClientSection" x-transition x-cloak>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <label for="customerName" class="form-label fw-bold">Nombre del Cliente</label>
                                <input type="text" id="customerName" class="form-control" x-model="customerName" placeholder="Nombre del Cliente" :disabled="isProcessing">
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <label for="customerEmail" class="form-label fw-bold">Email del Cliente</label>
                                <input type="email" id="customerEmail" class="form-control" x-model="customerEmail" placeholder="Email del Cliente" :disabled="isProcessing">
                            </div>
                            
                        </div>

                        <!-- Footer de la venta -->
                        <div class="bg-light-accent col-12 rounded-3 p-4 d-flex flex-wrap justify-content-between align-items-center">
                            
                            <div class="col-4 text-muted small fw-bold">
                                Subtotal: <span x-text="salesHeaderData.subtotal > 0 ? salesHeaderData.subtotal + ' €' : '0.00 €'"></span><br>
                                Impuestos: <span x-text="salesHeaderData.tax > 0 ? salesHeaderData.tax + ' €' : '0.00 €'"></span>
                            </div>

                            <div class="col-4 text-center fw-bold">
                                <span class="fw-bold fs-5">
                                    Total:
                                    <span x-text="salesHeaderData.total > 0 ? salesHeaderData.total + ' €' : '0.00 €'"></span>
                                </span>
                            </div>

                            <div class="col-4 align-items-center text-center">

                                <label for="paymentType" class="form-label fw-bold mb-1">Método de pago</label>
                                <select id="paymentType" class="form-select mb-2" x-model="paymentType">
                                    <option value="1">EFECTIVO</option>
                                    <option value="2">BIZUM</option>
                                    <option value="3">TPV</option>
                                </select>

                                <button class="btn btn-success fw-bold btn-lg col-10"
                                :disabled="saleItems.length === 0 || isProcessing" @click="registerSale">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Registrar Venta
                                </button>

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
