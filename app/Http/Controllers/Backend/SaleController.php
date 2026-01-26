<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Client;
use App\Models\Product;
use App\Models\ReturnItems;
use App\Models\Settings;
use App\Models\Tax;
use Illuminate\Support\Facades\Mail;
use App\Repositories\SalesRepository;

class SaleController extends Controller {

    protected $salesRepository;

    public function __construct(SalesRepository $salesRepository){
        $this->salesRepository = $salesRepository;
    }

    /**
     * Funcion encargada de obtener la vista principal de ventas
     *
     * @return \Illuminate\View\View
    */
    public function index() {

        // Obtener los productos activos, categorías y el historial de ventas
        $activeProducts = $this->salesRepository->getActiveProducts();
        $categories = $this->salesRepository->getCategories();
        $salesHistory = $this->salesRepository->getSalesHistory();

        // Retornar la vista con los datos necesarios
        // Asegúrate de que las vistas y los datos estén correctamente configurados
        return view('backend.sales.index', compact('activeProducts', 'categories', 'salesHistory'));
    
    }

    /**
     * Funcion encargada de guardar una venta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function save(Request $request) {

        $data = $request->all();

        // Validar los datos recibidos
        $validated = $request->validate([
            'salesHeaderData.subtotal' => 'required|numeric|min:0',
            'salesHeaderData.tax' => 'required|numeric|min:0',
            'salesHeaderData.total' => 'required|numeric|min:0',
            'saleItems' => 'required|array|min:1',
            'saleItems.*.id' => 'required|exists:products,id',
            'saleItems.*.quantity' => 'required|integer|min:1',
            'saleItems.*.sale_price' => 'required|numeric|min:0',
            'salesHeaderData.payment_type' => 'required|in:1,2,3',
        ]);
        
        $consecutive = Sale::count() + 1;
        $code = 'VENTA-' . str_pad($consecutive, 6, '0', STR_PAD_LEFT);

        // Verificar si se ha proporcionado un cliente
        if (isset($data['salesHeaderData']['client_name']) && $data['salesHeaderData']['client_name'] != null) {
            
            $client = Client::create([
                'id' => Str::uuid()->toString(),
                'name' => $data['salesHeaderData']['client_name'],
                'email' => $data['salesHeaderData']['client_email'] ?? null,
                'phone' => $data['salesHeaderData']['client_phone'] ?? null,
                'address' => $data['salesHeaderData']['client_address'] ?? null
            ]);

        }

        // Crear la venta (Sales)
        $sale = Sale::create([
            'id' => Str::uuid()->toString(),
            'client_id' => $client->id ?? null,
            'consecutive' => $consecutive,
            'code' => $code,
            'subtotal' => $validated['salesHeaderData']['subtotal'],
            'tax' => $validated['salesHeaderData']['tax'],
            'total' => $validated['salesHeaderData']['total'],
            'currency' => 'EUR',
            'status' => Sale::ACTIVE,
            'type_payment' => $validated['salesHeaderData']['payment_type'],
            'notes' => null
        ]);

        // Crear los detalles de la venta (SalesDetail)
        foreach ($validated['saleItems'] as $item) {
            
            SaleDetail::create([
                'id' => Str::uuid()->toString(),
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unitary_price' => $item['sale_price'],
                'subtotal' => $item['quantity'] * $item['sale_price']
            ]);

            // Restamos la cantidad del producto vendido
            $product = Product::findOrFail($item['id']);

            if ($product->quantity < $item['quantity']) {

                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock para el producto: ' . $product->name,
                ], 400);

            }

            $product->quantity -= $item['quantity'];
            $product->save();

        }

        // Enviar correo si se proporcionó un email del cliente
        if (isset($client) && $client->email) {
            $settings = Settings::first();
            Mail::to($client->email)->send(new \App\Mail\InvoiceMail($sale, $settings));
        }

        // Retornar una respuesta JSON indicando éxito
        return response()->json([
            'success' => true,
            'message' => 'Venta registrada correctamente',
        ]);

    }

    /**
     * Obtiene los detalles de una venta específica.
     *
     * @param string $id - ID de la venta
     * @return \Illuminate\Http\JsonResponse
    */
    public function detail($id) {

        // Validar que la venta exista
        $sale = Sale::with(['items', 'client', 'returnItems'])->findOrFail($id);

        // Retornar una respuesta JSON con los detalles de la venta
        return response()->json([
            'id' => $sale->id,
            'code' => $sale->code,
            'sale_date' => \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d h:i A'),
            'payment_type' => $sale->type_payment,
            'total' => number_format($sale->total, 2),
            'subtotal' => number_format($sale->subtotal, 2),
            'tax' => number_format($sale->tax, 2),
            'client_name' => $sale->client->name ?? null,
            'client_email' => $sale->client->email ?? null,
            'items' => $sale->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->producto->name,
                    'quantity' => $item->quantity,
                    'sale_price' => number_format($item->producto->sale_price, 2),
                    'tax' => $item->producto->taxable ? number_format($item->producto->tax->rate, 2) : '0.00',
                    'tax_value' => number_format(($item->quantity * $item->producto->sale_price) * $item->producto->tax->rate / 100, 2),
                    'total' => number_format($item->quantity * $item->producto->sale_price, 2)
                ];
            }),
            'returns' => $sale->returnItems->map(function($return) {

                $taxRate = $taxValue = 0;

                if ($return->saleDetail && $return->saleDetail->producto && $return->saleDetail->producto->tax) {
                    $taxRate = $return->saleDetail->producto->tax->rate;
                    $taxValue = number_format(($return->quantity * $return->saleDetail->unitary_price) * $taxRate / 100, 2);
                }

                $total_sale = number_format(($return->quantity * $return->saleDetail->unitary_price + $taxValue), 2);

                
                // Retornar los detalles de la devolución
                return [
                    'id' => $return->id,
                    'sale_detail_id' => $return->sale_detail_id,
                    'user_id' => $return->user_id,
                    'quantity' => $return->quantity,
                    'note' => $return->note,
                    'tax_value' => $taxValue,
                    'total' => $total_sale,
                    'reason' => $return->reason,
                    'status' => $return->status,
                    'created_at' => \Carbon\Carbon::parse($return->created_at)->format('Y-m-d h:i A'),
                ];
            }),
        ]);

    }

    /**
     * Genera una factura para una venta específica.
     *
     * @param string $id - ID de la venta
     * @return \Illuminate\View\View
    */
    public function invoice($id) {

        // Validar que la venta exista
        $sale = Sale::with(['items', 'client'])->findOrFail($id);

        $settings = Settings::first();

        // Retornar una vista con los detalles de la venta para imprimir
        return view('backend.templates.invoice', compact('sale', 'settings'));

    }

    /**
     * Muestra el formulario de devolución de productos.
     *
     * @param string $id - ID de la venta
     * @return \Illuminate\View\View
    */
    public function refundForm($id) {

        // Validar que la venta exista
        $sale = Sale::with('items.producto')->findOrFail($id);

        // Retornar una vista con el formulario de devolución
        return view('backend.sales.returns-modal', compact('sale'));

    }

    /**
     * Procesa la devolución de productos de una venta.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id - ID de la venta
     * @return \Illuminate\Http\JsonResponse
    */
    public function processRefund(Request $request, $id) { 

        // Validar que la venta exista
        $sale = Sale::with(['items'])->findOrFail($id);

        // Validar los datos de la devolución
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.sale_detail_id' => 'required|exists:sale_details,id',
            'items.*.sale_id' => 'required|exists:sales,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'nullable|string|max:255',
            'items.*.note' => 'nullable|string|max:500',
        ]);

        
        // Procesar cada producto a devolver
        foreach ($validated['items'] as $item) {

            $saleDetail = SaleDetail::findOrFail($item['sale_detail_id']);

            if ($saleDetail->quantity < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente cantidad para devolver del producto: ' . $saleDetail->product->name,
                ], 400);
            }

            // Aqui se crean los registros de devolucion
            $refund = new ReturnItems();

            $refund->sale_id = $sale->id;
            $refund->sale_detail_id = $saleDetail->id;
            $refund->user_id = auth()->user()->id;
            $refund->quantity = $item['quantity'];
            $refund->note = $item['note'] ?? null;
            $refund->status = ReturnItems::PROCESSED;
            $refund->reason = $item['reason'] ?? null;

            $refund->save();

            // Actualizar la cantidad del producto vendido
            if ($item['reason'] == ReturnItems::RESTOCK) {

                // Aumentar la cantidad del producto en inventario
                $product = Product::findOrFail($saleDetail->product_id);
                $product->quantity += $item['quantity'];
                $product->save();

            }

            $saleDetail->quantity -= $item['quantity'];
            $saleDetail->save();

            
        }

        return response()->json([
            'success' => true,
            'message' => 'Devolución procesada correctamente',
        ]);
    }

    /**
     * Envía la factura de una venta por correo electrónico.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id - ID de la venta
     * @return \Illuminate\Http\JsonResponse
    */
    public function sendEmail(Request $request, $id)
    {
        $sale = Sale::with(['items.producto.tax', 'client'])->findOrFail($id);
        $email = $request->input('email');

        $settings = Settings::first();

        // Usa la librería de correos de Laravel
        Mail::to($email)->send(new \App\Mail\InvoiceMail($sale, $settings));

        return response()->json(['success' => true]);
    }
}