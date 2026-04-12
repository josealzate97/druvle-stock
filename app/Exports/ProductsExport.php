<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query()->with([
            'sizes' => function ($q) {
                $q->where('status', ProductSize::ACTIVE)
                    ->select('id', 'product_id', 'price', 'quantity');
            }
        ]);

        if (!empty($this->filters['from'])) {
            $query->whereDate('creation_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('creation_date', '<=', $this->filters['to']);
        }

        $query->orderBy('creation_date', 'desc')->orderBy('name', 'asc');

        return $query->get([
            'id',
            'name',
            'creation_date',
            'quantity',
            'purchase_price',
            'sale_price',
            'has_sizes',
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
        $sizes = $product->sizes ?? collect();
        $sizeQuantity = (int) $sizes->sum('quantity');
        $sizeStockValue = (float) $sizes->sum(function ($size) {
            return (float) $size->price * (int) $size->quantity;
        });
        $sizeAveragePrice = $sizeQuantity > 0
            ? $sizeStockValue / $sizeQuantity
            : (float) ($sizes->avg('price') ?? 0);

        $quantity = $product->has_sizes ? $sizeQuantity : (int) ($product->quantity ?? 0);
        $purchasePrice = $product->has_sizes ? '-' : number_format((float) ($product->purchase_price ?? 0), 2, '.', '');
        $salePrice = $product->has_sizes
            ? ($sizeAveragePrice > 0 ? number_format($sizeAveragePrice, 2, '.', '') : '-')
            : number_format((float) ($product->sale_price ?? 0), 2, '.', '');

        return [
            $product->name,
            $product->creation_date ? \Carbon\Carbon::parse($product->creation_date)->format('d/m/Y') : '',
            $quantity,
            $purchasePrice,
            $salePrice,
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
