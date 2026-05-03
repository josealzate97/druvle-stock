<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $settings;
    protected $user;
    protected int $headerRows = 4;

    public function __construct($filters = [], $settings = null, $user = null)
    {
        $this->filters  = $filters;
        $this->settings = $settings;
        $this->user     = $user;
    }

    public function collection()
    {
        $query = Product::query()->with([
            'sizes' => function ($q) {
                $q->where('status', ProductSize::ACTIVE)
                    ->select('id', 'product_id', 'name', 'price', 'quantity', 'creation_date');
            }
        ]);

        if (!empty($this->filters['from'])) {
            $query->whereDate('creation_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('creation_date', '<=', $this->filters['to']);
        }

        $query->orderBy('creation_date', 'desc')->orderBy('name', 'asc');

        return $query->get()->flatMap(function (Product $product) {
            $sizes = $product->sizes ?? collect();

            if ($product->has_sizes && $sizes->isNotEmpty()) {
                return $sizes->map(function ($size) use ($product) {
                    return (object) [
                        'row_name'     => $product->name,
                        'row_size'     => $size->name,
                        'row_date'     => $size->creation_date ?? $product->creation_date,
                        'row_quantity' => (int) $size->quantity,
                        'row_purchase' => null,
                        'row_sale'     => (float) $size->price > 0 ? (float) $size->price : null,
                    ];
                });
            }

            return collect([(object) [
                'row_name'     => $product->name,
                'row_size'     => null,
                'row_date'     => $product->creation_date,
                'row_quantity' => (int) ($product->quantity ?? 0),
                'row_purchase' => $product->purchase_price !== null ? (float) $product->purchase_price : null,
                'row_sale'     => $product->sale_price !== null ? (float) $product->sale_price : null,
            ]]);
        });
    }

    public function headings(): array
    {
        return ['Nombre', 'Talla', 'Fecha entrada', 'Cantidad', 'Precio compra', 'Precio venta'];
    }

    public function map($row): array
    {
        return [
            $row->row_name,
            $row->row_size ?? '—',
            $row->row_date ? \Carbon\Carbon::parse($row->row_date)->format('d/m/Y') : '',
            $row->row_quantity,
            $row->row_purchase !== null ? '$ ' . number_format($row->row_purchase, 2, ',', '.') : '—',
            $row->row_sale     !== null ? '$ ' . number_format($row->row_sale,     2, ',', '.') : '—',
        ];
    }

    public function registerEvents(): array
    {
        $settings   = $this->settings;
        $user       = $this->user;
        $headerRows = $this->headerRows;
        $lastCol    = 'F';

        return [
            AfterSheet::class => function (AfterSheet $event) use ($settings, $user, $headerRows, $lastCol) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, $headerRows);

                $company = $settings->company_name ?? 'DRUVLE';
                $parts   = array_filter([
                    $settings->phone   ? 'Tel: ' . $settings->phone   : null,
                    $settings->address ? 'Dir: ' . $settings->address : null,
                ]);
                $contact = implode('   |   ', $parts);
                $genBy   = 'Generado por: ' . ($user ? $user->name : 'Sistema')
                         . '   |   Fecha: ' . now()->format('d/m/Y H:i:s');

                $sheet->setCellValue('A1', $company);
                $sheet->setCellValue('A2', $contact);
                $sheet->setCellValue('A3', $genBy);
                $sheet->setCellValue('A4', '');

                foreach ([1, 2, 3, 4] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                }

                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F0FE']],
                ]);
                foreach (['A2', 'A3'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font'      => ['size' => 10],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F4FF']],
                    ]);
                }

                $headingRow = $headerRows + 1;
                $sheet->getStyle("A{$headingRow}:{$lastCol}{$headingRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $totalRows = $sheet->getHighestRow();
                $sheet->getStyle("A1:{$lastCol}{$totalRows}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getRowDimension(2)->setRowHeight(16);
                $sheet->getRowDimension(3)->setRowHeight(16);
            },
        ];
    }
}

