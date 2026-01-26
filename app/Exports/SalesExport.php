<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Sale::with('client');

        if (!empty($this->filters['from'])) {
            $query->whereDate('sale_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('sale_date', '<=', $this->filters['to']);
        }

        $query->orderBy('sale_date', 'desc');

        return $query->get();

    }

    public function headings(): array
    {
        return [
            'Código',
            'Cliente',
            'Fecha de venta',
            'Subtotal (€)',
            'Impuesto (€)',
            'Total (€)',
            'Tipo de pago'
        ];
    }

    /**
     * Mapea y formatea los datos para cada fila.
     */
    public function map($sale): array
    {
        return [
            $sale->code,
            $sale->client_name ?? 'Anónimo',
            \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y'), // Formato de fecha
            round($sale->subtotal, 2), // Subtotal en flotante con 2 decimales
            round($sale->tax, 2), // Impuesto en flotante con 2 decimales
            round($sale->total, 2), // Total en flotante con 2 decimales
            $sale->type_payment == 1 ? 'EFECTIVO' : ($sale->type_payment == 2 ? 'BIZUM' : 'TVP'),
        ];
    }

    /**
     * Define los anchos de las columnas.
    */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Código
            'B' => 25, // Cliente
            'C' => 20, // Fecha de venta
            'D' => 15, // Subtotal (€)
            'E' => 20, // Impuesto (€)
            'F' => 15, // Total (€)
            'G' => 20, // Tipo de pago
        ];
    }
}