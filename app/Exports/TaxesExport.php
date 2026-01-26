<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;


class TaxesExport implements FromCollection, WithHeadings, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('sales')
            ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('taxes', 'products.tax_id', '=', 'taxes.id')
            ->select('taxes.name as Impuesto', DB::raw('SUM(sale_details.quantity * products.sale_price * taxes.rate / 100) as Total_recaudado'));

        if (!empty($this->filters['from'])) {
            $query->whereDate('sales.sale_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('sales.sale_date', '<=', $this->filters['to']);
        }

        $query->groupBy('taxes.name');

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Impuesto',
            'Total recaudado'
        ];
    }

    /**
     * Define los anchos de las columnas.
    */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Impuesto
            'B' => 25, // Total recaudado
        ];
    }
}