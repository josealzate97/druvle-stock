<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Sale;
use App\Models\Tax;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\DB;

class ReportsRepository {
    
    public function getProductsReport($filters = [])
    {
        $query = Product::query()->with([
            'sizes' => function ($q) {
                $q->where('status', ProductSize::ACTIVE)
                    ->select('id', 'product_id', 'name', 'price', 'quantity', 'creation_date');
            }
        ]);

        if (!empty($filters['from'])) {
            $query->whereDate('creation_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('creation_date', '<=', $filters['to']);
        }

        $query->orderBy('creation_date', 'desc')->orderBy('name', 'asc');

        return $query->get()->flatMap(function (Product $product) {
            $sizes = $product->sizes ?? collect();

            // Producto con tallas: una fila por cada talla activa
            if ($product->has_sizes && $sizes->isNotEmpty()) {
                return $sizes->map(function ($size) use ($product) {
                    $qty   = (int) $size->quantity;
                    $price = (float) $size->price;
                    return [
                        'id'             => $product->id . '-' . $size->id,
                        'name'           => $product->name,
                        'size_name'      => $size->name,
                        'creation_date'  => $size->creation_date ?? $product->creation_date,
                        'quantity'       => $qty,
                        'purchase_price' => null,
                        'sale_price'     => $price > 0 ? $price : null,
                        'stock_value'    => $price * $qty,
                        'has_sizes'      => true,
                    ];
                });
            }

            // Producto sin tallas: fila única
            $quantity   = (int) ($product->quantity ?? 0);
            $salePrice  = $product->sale_price !== null ? (float) $product->sale_price : null;
            $stockValue = (float) ($product->sale_price ?? 0) * $quantity;

            return collect([[
                'id'             => $product->id,
                'name'           => $product->name,
                'size_name'      => null,
                'creation_date'  => $product->creation_date,
                'quantity'       => $quantity,
                'purchase_price' => $product->purchase_price !== null ? (float) $product->purchase_price : null,
                'sale_price'     => $salePrice,
                'stock_value'    => $stockValue,
                'has_sizes'      => false,
            ]]);
        })->values();
    }

    public function getSalesReport($filters = [])
    {
        $query = Sale::with('client');

        if (!empty($filters['from'])) {
            $query->whereDate('sale_date', '>=', $filters['from']);
        }
        
        if (!empty($filters['to'])) {
            $query->whereDate('sale_date', '<=', $filters['to']);
        }

        return $query->orderBy('sale_date', 'desc')->get();
    }

    public function getTaxesReport($filters = [])
    {
        $tenantId = TenantScope::resolveTenantId();

        $query = DB::table('sales')
            ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('taxes', 'products.tax_id', '=', 'taxes.id')
            ->whereNotNull('products.tax_id')
            ->when($tenantId, fn($q) => $q->where('sales.tenant_id', $tenantId))
            ->select(
                'taxes.id',
                'taxes.name',
                DB::raw('COALESCE(SUM((sale_details.subtotal * taxes.rate) / 100), 0) as total_tax')
            );

        if (!empty($filters['from'])) {
            $query->whereDate('sales.sale_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('sales.sale_date', '<=', $filters['to']);
        }

        return $query
            ->groupBy('taxes.id', 'taxes.name')
            ->orderBy('taxes.name', 'asc')
            ->get();

    }
}
