<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query();

        if (!empty($this->filters['from'])) {
            $query->whereDate('creation_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('creation_date', '<=', $this->filters['to']);
        }

        $query->orderBy('creation_date', 'desc')->orderBy('name', 'asc');

        return $query->get([
            'name',
            'creation_date',
            'quantity',
            'purchase_price',
            'sale_price'
        ]);
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Fecha de entrada',
            'Cantidad',
            'Precio compra (€)',
            'Precio venta (€)'
        ];
    }

    /**
     * Mapea y formatea los datos para cada fila.
    */
    public function map($product): array
    {
        return [
            $product->name,
            \Carbon\Carbon::parse($product->creation_date)->format('d/m/Y'), // Formato de fecha
            $product->quantity,
            round($product->purchase_price, 2), // Precio de compra con 2 decimales
            round($product->sale_price, 2), // Precio de venta con 2 decimales
        ];
    }

    /**
     * Define el ancho de las columnas.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Nombre
            'B' => 20, // Fecha de entrada
            'C' => 15, // Cantidad
            'D' => 20, // Precio compra
            'E' => 20, // Precio venta
        ];
    }
}