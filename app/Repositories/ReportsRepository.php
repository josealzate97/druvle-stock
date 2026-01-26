<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;

class ReportsRepository {
    
    public function getProductsReport($filters = [])
    {
        $query = Product::query();

        if (!empty($filters['from'])) {
            $query->whereDate('creation_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('creation_date', '<=', $filters['to']);
        }

        $query->orderBy('creation_date', 'desc')->orderBy('name', 'asc');

        return $query->get();
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