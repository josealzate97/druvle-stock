
<!DOCTYPE html>

<html lang="es">
    
    <head>

        <meta charset="UTF-8">
        
        <title>Factura #{{ $sale->code ?? '' }}</title>

        <!-- Vite Assets -->
        @vite(['resources/css/vendor.css', 'resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Estilo Ticket Térmico */
            body {
                margin: 0;
                padding: 32px 16px;
                background: #f4f6fb;
                font-family: 'Manrope', 'Segoe UI', sans-serif;
                color: #0f172a;
            }
            
            .yaslo-invoice-box {
                max-width: 760px;
                margin: 20px auto;
                padding: 28px 32px;
                background: #fff;
                border: 1px solid #e6ebf2;
                border-radius: 18px;
                font-size: 13px;
                line-height: 1.5;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            }
            
            .yaslo-invoice-header {
                display: flex;
                justify-content: space-between;
                gap: 2rem;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 16px;
                margin-bottom: 16px;
            }
            
            .yaslo-invoice-title {
                font-size: 22px;
                font-weight: 800;
                color: #1b77d3;
                margin-bottom: 6px;
            }
            
            .company-info {
                font-size: 12px;
                line-height: 1.5;
                color: #475569;
            }
            
            .yaslo-invoice-info {
                margin: 14px 0;
                font-size: 12px;
                color: #475569;
                display: grid;
                gap: 6px;
            }
            
            .yaslo-invoice-client {
                margin: 16px 0 20px 0;
                font-size: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 12px 14px;
                background: #f8fafc;
            }
            
            .yaslo-invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 12px;
                font-size: 12px;
            }
            
            .yaslo-invoice-table th {
                background: #f1f5f9;
                color: #475569;
                font-weight: 700;
                padding: 10px 8px;
                border-bottom: 1px solid #e5e7eb;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                font-size: 10px;
            }
            
            .yaslo-invoice-table td {
                padding: 10px 8px;
                border-bottom: 1px solid #eef2f7;
                font-size: 12px;
            }
            
            .yaslo-invoice-table .text-right {
                text-align: right;
            }
            
            .yaslo-invoice-summary {
                border-top: 1px solid #e5e7eb;
                padding-top: 12px;
                margin-top: 12px;
                font-size: 12px;
                display: grid;
                gap: 6px;
            }
            
            .summary-row {
                display: flex;
                justify-content: space-between;
                color: #475569;
                font-weight: 600;
            }
            
            .summary-row.total {
                font-weight: 800;
                font-size: 14px;
                margin-top: 6px;
                padding-top: 8px;
                border-top: 1px dashed #e2e8f0;
                color: #1b77d3;
            }
            
            .yaslo-invoice-footer {
                margin-top: 18px;
                text-align: center;
                font-size: 10px;
                border-top: 1px solid #eef2f7;
                padding-top: 12px;
                line-height: 1.4;
                color: #64748b;
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
            
            .text-left {
                text-align: left !important;
            }

            /* Botones personalizados */
            .yaslo-btn {
                padding: 10px 18px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
                border-radius: 10px;
                margin: 5px;
                color: #0f172a;
            }
            .yaslo-btn-print {
                background: #eef2f7;
            }
            .yaslo-btn-print:hover {
                background: #e2e8f0;
            }
            .yaslo-btn-email {
                background: #1b77d3;
                color: #fff;
                border-color: #1b77d3;
            }
            .yaslo-btn-email:hover {
                background: #155fb8;
            }

            /* Centrado de botones */
            .yaslo-btn-group {
                display: flex;
                justify-content: center;
                gap: 0.5rem;
                margin-top: 1.5rem;
            }
            
            @media print {
                body {
                    background: #fff;
                    padding: 0;
                }
                .yaslo-invoice-box {
                    box-shadow: none;
                    margin: 0;
                    border: none;
                    border-radius: 0;
                }
                .yaslo-btn-group {
                    display: none !important;
                }
            }

            /* Modal personalizado */
            .yaslo-modal-bg {
                display: none;
                position: fixed;
                top: 0; left: 0;
                width: 100vw; height: 100vh;
                background: rgba(30,174,107,0.12);
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            .yaslo-modal-bg.active {
                display: flex;
            }
            .yaslo-modal-content {
                background: #fff;
                padding: 2.5rem 2rem;
                border-radius: 12px;
                min-width: 320px;
                box-shadow: 0 4px 32px rgba(84, 94, 89, 0.13);
                position: relative;
                animation: yasloModalIn 0.3s;
            }
            
            @keyframes yasloModalIn {
                from { transform: scale(0.95); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }

            .yaslo-modal-close {
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
                border: 1px solid #d1e7dd;
                border-radius: 6px;
                transition: border-color 0.2s, box-shadow 0.2s;
                box-sizing: border-box;
            }

            .form-control:focus {
                border-color: #1eae6b;
                outline: none;
                box-shadow: 0 0 0 2px rgba(30,174,107,0.15);
            }

        </style>

    </head>

    <body>

        <div class="yaslo-invoice-box">
            
            <div class="yaslo-invoice-header">
                <div class="yaslo-invoice-title">{{ strtoupper($settings->company_name ?? 'NOMBRE EMPRESA') }}</div>
                <div class="company-info">
                    {{ $settings->address ?? 'Dirección de la empresa' }}<br>
                    TEL: {{ $settings->phone ?? '000 000 000' }}<br>
                    NIF: {{ $settings->nit ?? '' }}<br>
                    {{ $settings->email ?? '' }}
                </div>
            </div>

            <div class="yaslo-invoice-info">
                <div style="display: flex; justify-content: space-between;">
                    <span>FACTURA No:</span>
                    <span><strong>{{ $sale->code ?? '' }}</strong></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>FECHA:</span>
                    <span>{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') : '' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>PAGO:</span>
                    <span>{{ $sale->type_payment == 1 ? 'EFECTIVO' : ($sale->type_payment == 2 ? 'BIZUM' : 'TPV') }}</span>
                </div>
            </div>

            <div class="yaslo-invoice-client">
                <strong>CLIENTE:</strong> {{ strtoupper($sale->client->name ?? 'ANÓNIMO') }}
                @if(!empty($sale->client->email))
                    <br><strong>EMAIL:</strong> {{ $sale->client->email }}
                @endif
                @if(!empty($sale->client->phone))
                    <br><strong>TEL:</strong> {{ $sale->client->phone }}
                @endif
            </div>

            <table class="yaslo-invoice-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">COD</th>
                        <th style="width: 30%;">PRODUCTO</th>
                        <th style="width: 10%;">TOTAL</th>
                        <th style="width: 10%;" class="text-right">PRECIO</th>
                        <th style="width: 15%;" class="text-right">IMPORTE</th>
                        <th style="width: 15%;" class="text-right">T. IMP</th>
                        <!-- <th style="width: 20%;" class="text-right">TOTAL</th> -->
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
                            <td>{{ $item->producto->code ?? '' }}</td>
                            <td>{{ strtoupper(substr($item->producto->name ?? '', 0, 25)) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->producto->sale_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->quantity * $item->producto->sale_price, 2) }}</td>
                            <td class="text-right">%{{ $item->producto->taxable ? number_format($item->producto->tax->rate, 2) : '0.00' }}</td>
                            <!-- <td class="text-right">{{ number_format($itemTotal, 2) }}</td> -->
                        </tr>

                    @endforeach

                </tbody>

            </table>

            <div class="yaslo-invoice-summary">

                <div class="summary-row">
                    <span>IMPORTE BRUTO:</span>
                    <span>{{ number_format($sale->subtotal, 2) }} €</span>
                </div>

                <div class="summary-row">
                    <span>IMPUESTOS:</span>
                    <span>{{ number_format($sale->tax, 2) }} €</span>
                </div>

                <div class="summary-row total">
                    <span>TOTAL FACTURA:</span>
                    <span>{{ number_format($sale->total, 2) }} €</span>
                </div>

            </div>

            <div class="yaslo-invoice-footer text-left">
                Reg. Merc. Madrid Tomo 36818, folio 99, seccion 8, hoja M - 382167, 
                Inscripcion 17a ENV/2023/000007820;ENV/2023/000007870<br>
                PRECIOS SEGUN TARIFA REFERENCIA, SUJETOS A CONDICIONES COMERCIALES.<br>
                LAS PARTES SE SOMETEN EXPRESAMENTE A LA JURISDICCION DE LOS JUZGADOS.<br>
                <br>
                
            </div>

            <div class="yaslo-invoice-footer text-center">
                <strong>MUCHAS GRACIAS</strong><br>
                YASLO © {{ date('Y') }}
            </div>

            @if(empty($isEmail))
                <div class="yaslo-btn-group">
                    <button class="yaslo-btn yaslo-btn-print" onclick="window.print()">Imprimir (Ctrl + P)</button>
                    <button class="yaslo-btn yaslo-btn-email" onclick="openEmailModal()">Enviar por Email</button>
                </div>
           @endif 

        </div>

        <div id="emailModal" class="yaslo-modal-bg">

            <div class="yaslo-modal-content">
                
                <button class="yaslo-modal-close" onclick="closeEmailModal()">&times;</button>
                <h3 class="mb-3" style="color:#1eae6b;">Enviar factura por email</h3>
                
                <label class="fw-bold mb-2">Email destino</label><br>
                <input type="email" id="emailDestino" class="form-control mb-3" value="{{ $sale->client->email ?? '' }}">
                
                <div class="yaslo-btn-group">
                    <button class="yaslo-btn yaslo-btn-email" onclick="sendInvoiceEmail()">Enviar</button>
                    <button class="yaslo-btn yaslo-btn-danger" onclick="closeEmailModal()">Cancelar</button>
                </div>

                <div id="emailMsg" class="mt-2 text-center"></div>

            </div>

        </div>

    </body>

    <script>

        function openEmailModal() {
            document.getElementById('emailModal').style.display = 'flex';
            document.getElementById('emailDestino').disabled = false;
            document.querySelectorAll('.yaslo-btn-group button').forEach(btn => btn.disabled = false);
            document.getElementById('emailMsg').innerText = '';
        }

        function closeEmailModal() {
            document.getElementById('emailModal').style.display = 'none';
            document.getElementById('emailMsg').innerText = '';
        }

        function sendInvoiceEmail() {

            const emailInput = document.getElementById('emailDestino');
            const btns = document.querySelectorAll('.yaslo-btn-group button');
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
