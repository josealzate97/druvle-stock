
<!DOCTYPE html>

<html lang="es">
    
    <head>

        <meta charset="UTF-8">
        
        <title>Factura #{{ $sale->code ?? '' }}</title>

        <!-- Vite Assets -->
        @vite(['resources/css/vendor.css', 'resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Puedes mover esto a templates.css */
            .druvle-invoice-box {
                max-width: 800px;
                margin: 30px auto;
                padding: 32px;
                border: 1px solid #e0e0e0;
                background: #fff;
                font-family: 'Segoe UI', Arial, sans-serif;
                color: #222;
                font-size: 16px;
            }
            .druvle-invoice-header {
                border-bottom: 2px solid #1b77d3;
                padding-bottom: 12px;
                margin-bottom: 24px;
            }
            .druvle-invoice-title {
                color: #1b77d3;
                font-size: 2rem;
                font-weight: bold;
            }
            .druvle-invoice-info, .druvle-invoice-client {
                margin-bottom: 16px;
            }
            .druvle-invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 24px;
            }
            .druvle-invoice-table th {
                background: #f5f5f5;
                color: #1b77d3;
                font-weight: bold;
                padding: 8px;
                border-bottom: 1px solid #e0e0e0;
            }
            .druvle-invoice-table td {
                padding: 8px;
                border-bottom: 1px solid #e0e0e0;
            }
            .druvle-invoice-summary {
                text-align: right;
                margin-top: 16px;
            }
            .druvle-invoice-summary span {
                display: inline-block;
                min-width: 120px;
            }
            .druvle-invoice-footer {
                margin-top: 32px;
                text-align: center;
                color: #888;
                font-size: 14px;
            }

            .mb-1 {
                margin-bottom: 0.25rem !important;
            }
            .mb-2 {
                margin-bottom: 0.5rem !important;
            }
            .mb-3 {
                margin-bottom: 1rem !important;
            }
            .mb-4 {
                margin-bottom: 1.5rem !important;
            }
            .mb-5 {
                margin-bottom: 3rem !important;
            }

            .mt-1 {
                margin-top: 0.25rem !important;
            }
            .mt-2 { 
                margin-top: 0.5rem !important;
            }

            .fw-bold {
                font-weight: bold !important;
            }

            .fs-3 {
                font-size: 1.5rem !important;
            }

            .w-50 {
                width: 50% !important;
            }

            .w-100 {
                width: 100% !important;
            }

            .d-flex {
                display: flex !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-right {
                text-align: right !important;
            }

            /* Botones personalizados */
            .druvle-btn {
                padding: 0.6rem 1.5rem;
                border: none;
                border-radius: 6px;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: background 0.2s, box-shadow 0.2s;
                margin: 0 0.5rem;
                box-shadow: 0 2px 8px rgba(27,119,211,0.07);
            }
            .druvle-btn-print {
                background: #e0e0e0;
                color: #222;
            }
            .druvle-btn-print:hover {
                background: #bdbdbd;
            }
            .druvle-btn-email {
                background: #1b77d3;
                color: #fff;
            }
            .druvle-btn-email:hover {
                background: #155fb8;
            }

            /* Centrado de botones */
            .druvle-btn-group {
                display: flex;
                justify-content: center;
                gap: 1rem;
                margin-top: 2rem;
            }

            /* Modal personalizado */
            .druvle-modal-bg {
                display: none;
                position: fixed;
                top: 0; left: 0;
                width: 100vw; height: 100vh;
                background: rgba(27,119,211,0.12);
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            .druvle-modal-bg.active {
                display: flex;
            }
            .druvle-modal-content {
                background: #fff;
                padding: 2.5rem 2rem;
                border-radius: 12px;
                min-width: 320px;
                box-shadow: 0 4px 32px rgba(84, 94, 89, 0.13);
                position: relative;
                animation: druvleModalIn 0.3s;
            }
            
            @keyframes druvleModalIn {
                from { transform: scale(0.95); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }

            .druvle-modal-close {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                font-size: 1.3rem;
                color: #f34b4bff;
                cursor: pointer;
            }

            .form-control {
                display: block;
                width: 100%;
                padding: 0.5rem 0.75rem;
                font-size: 1rem;
                color: #222;
                background-color: #f8f9fa;
                border: 1px solid #dbe9ff;
                border-radius: 6px;
                transition: border-color 0.2s, box-shadow 0.2s;
                box-sizing: border-box;
            }

            .form-control:focus {
                border-color: #1b77d3;
                outline: none;
                box-shadow: 0 0 0 2px rgba(27,119,211,0.15);
            }

        </style>

    </head>

    <body>

        <div class="druvle-invoice-box">
            
            <div class="druvle-invoice-header d-flex align-items-center">
                
                <div class="w-50">
                    <div class="druvle-invoice-title">{{ $settings->company_name ?? 'Nombre Empresa' }}</div>
                    <div><strong>NIF:</strong> {{ $settings->nit ?? '' }}</div>
                    <div>{{ $settings->address ?? '' }}</div>
                    <div>{{ $settings->email ?? '' }}</div>
                </div>

                <div class="w-50 mt-1" style="text-align:right;">
                    <div class="fw-bold fs-3 mb-1">Factura #{{ $sale->code ?? '' }}</div>
                    <div class="druvle-invoice-info">
                        <strong>Fecha:</strong> {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d h:i A') : '' }}<br>
                        <strong>Tipo de pago:</strong> {{ $sale->type_payment == 1 ? 'EFECTIVO' : ($sale->type_payment == 2 ? 'BIZUM' : 'TPV') }}
                    </div>
                </div>

            </div>

            <div class="druvle-invoice-client mt-4 mb-4">
                <strong>Cliente:</strong> {{ $sale->client->name ?? 'Anónimo' }}<br>
                @if(!empty($sale->client->email))
                    <strong>Email:</strong> {{ $sale->client->email }}
                @endif
            </div>

            <table class="druvle-invoice-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    
                    @foreach($sale->items as $item)

                        @php

                            $taxValue = ($item->producto && $item->producto->taxable && isset($item->producto->tax->rate))
                                ? ($item->producto->sale_price * $item->quantity * $item->producto->tax->rate / 100)
                                : 0;
                            $itemTotal = ($item->quantity * $item->producto->sale_price) + $taxValue;

                        @endphp

                        <tr>
                            <td>{{ $item->producto->name ?? '' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ number_format($item->producto->sale_price, 2) }} €</td>
                            <td class="text-center">{{ number_format($taxValue, 2) }} €</td>
                            <td class="text-center">{{ number_format($itemTotal, 2) }} €</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

            <div class="druvle-invoice-summary mt-2">
                <div class="mt-2"><strong>Subtotal:</strong> {{ number_format($sale->subtotal, 2) }} €</div>
                <div class="mb-3"><strong>Impuestos:</strong> {{ number_format($sale->tax, 2) }} €</div>
                <div style="font-weight:bold; color:#1b77d3;"><span>Total:</span> {{ number_format($sale->total, 2) }} €</div>
            </div>

            <div class="druvle-invoice-footer">
                ¡Gracias por su compra!<br>
                DRUVLE - {{ date('Y') }}
            </div>

            @if(empty($isEmail))
                <div class="druvle-btn-group">
                    <button class="druvle-btn druvle-btn-print" onclick="window.print()">Imprimir (Ctrl + P)</button>
                    <button class="druvle-btn druvle-btn-email" onclick="openEmailModal()">Enviar por Email</button>
                </div>
           @endif 

        </div>

        <div id="emailModal" class="druvle-modal-bg">

            <div class="druvle-modal-content">
                
                <button class="druvle-modal-close" onclick="closeEmailModal()">&times;</button>
                <h3 class="mb-3" style="color:#1b77d3;">Enviar factura por email</h3>
                
                <label class="fw-bold mb-2">Email destino</label><br>
                <input type="email" id="emailDestino" class="form-control mb-3" value="{{ $sale->client->email ?? '' }}">
                
                <div class="druvle-btn-group">
                    <button class="druvle-btn druvle-btn-email" onclick="sendInvoiceEmail()">Enviar</button>
                    <button class="druvle-btn druvle-btn-danger" onclick="closeEmailModal()">Cancelar</button>
                </div>

                <div id="emailMsg" class="mt-2 text-center"></div>

            </div>

        </div>

    </body>

    <script>

        function openEmailModal() {
            document.getElementById('emailModal').style.display = 'flex';
            document.getElementById('emailDestino').disabled = false;
            document.querySelectorAll('.druvle-btn-group button').forEach(btn => btn.disabled = false);
            document.getElementById('emailMsg').innerText = '';
        }

        function closeEmailModal() {
            document.getElementById('emailModal').style.display = 'none';
            document.getElementById('emailMsg').innerText = '';
        }

        function sendInvoiceEmail() {

            const emailInput = document.getElementById('emailDestino');
            const btns = document.querySelectorAll('.druvle-btn-group button');
            const emailMsg = document.getElementById('emailMsg');

            const email = document.getElementById('emailDestino').value;
            const saleId = "{{ $sale->id }}";

             // Deshabilita campo y botones, muestra mensaje
            emailInput.disabled = true;
            btns.forEach(btn => btn.disabled = true);
            emailMsg.innerText = 'Enviando correo...';

            fetch(`/sales/send-email/${saleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            }).then(res => res.json()).then(data => {

                if (data.success) {

                    new Notyf().success('Factura enviada correctamente');
                    emailMsg.innerText = '¡Correo enviado correctamente!';

                } else {

                    new Notyf().error('Error al enviar la factura');
                    emailMsg.innerText = 'Error al enviar la factura.';

                }

            }).catch(() => {

                new Notyf().error('Error de red.');
                emailMsg.innerText = 'Error de red.';

            }).finally(() => {

                // Rehabilita campo y botones
                emailInput.disabled = false;
                btns.forEach(btn => btn.disabled = false);
                emailMsg.innerText = '';

                
            });

        }

    </script>

</html>
