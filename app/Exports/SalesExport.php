<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
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
        return ['Código', 'Cliente', 'Fecha de venta', 'Subtotal', 'Impuesto', 'Total', 'Tipo de pago'];
    }

    public function map($sale): array
    {
        $paymentTypes = [1 => 'EFECTIVO', 2 => 'TRANSFERENCIA', 3 => 'TPV'];

        return [
            $sale->code,
            $sale->client->name ?: 'Anónimo',
            Carbon::parse($sale->sale_date)->format('d/m/Y'),
            '$ ' . number_format((float) $sale->subtotal, 2, ',', '.'),
            '$ ' . number_format((float) $sale->tax,      2, ',', '.'),
            '$ ' . number_format((float) $sale->total,    2, ',', '.'),
            $paymentTypes[$sale->type_payment] ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        $settings   = $this->settings;
        $user       = $this->user;
        $headerRows = $this->headerRows;
        $lastCol    = 'G';

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
