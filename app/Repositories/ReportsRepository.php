<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Sale;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;

class ReportsRepository {
    
    public function getProductsReport($filters = [])
    {
        $query = Product::query()->with([
            'sizes' => function ($q) {
                $q->where('status', ProductSize::ACTIVE)
                    ->select('id', 'product_id', 'price', 'quantity');
            }
        ]);

        if (!empty($filters['from'])) {
            $query->whereDate('creation_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('creation_date', '<=', $filters['to']);
        }

        $query->orderBy('creation_date', 'desc')->orderBy('name', 'asc');

        return $query->get()->map(function (Product $product) {
            $sizes = $product->sizes ?? collect();

            $sizeQuantity = (int) $sizes->sum('quantity');
            $sizeStockValue = (float) $sizes->sum(function ($size) {
                return (float) $size->price * (int) $size->quantity;
            });
            $sizeAverageSalePrice = $sizeQuantity > 0
                ? $sizeStockValue / $sizeQuantity
                : (float) ($sizes->avg('price') ?? 0);

            $quantity = $product->has_sizes
                ? $sizeQuantity
                : (int) ($product->quantity ?? 0);

            $purchasePrice = $product->has_sizes
                ? null
                : ($product->purchase_price !== null ? (float) $product->purchase_price : null);

            $salePrice = $product->has_sizes
                ? ($sizeAverageSalePrice > 0 ? $sizeAverageSalePrice : null)
                : ($product->sale_price !== null ? (float) $product->sale_price : null);

            $stockValue = $product->has_sizes
                ? $sizeStockValue
                : ((float) ($product->sale_price ?? 0) * $quantity);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'creation_date' => $product->creation_date,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'sale_price' => $salePrice,
                'stock_value' => $stockValue,
                'has_sizes' => (bool) $product->has_sizes,
            ];
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

        // Ejemplo de consulta SQL compleja
        return DB::table('sales')
        ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
        ->join('products', 'sale_details.product_id', '=', 'products.id')
        ->join('taxes', 'products.tax_id', '=', 'taxes.id')
        ->select('taxes.name', DB::raw('SUM(sale_details.quantity * products.sale_price * taxes.rate / 100) as total_tax'))
        ->groupBy('taxes.name')
        ->get();

    }
}
