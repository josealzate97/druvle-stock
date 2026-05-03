<?php

namespace App\Exports;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class TaxesExport implements FromCollection, WithHeadings, WithMapping, WithEvents
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
        $tenantId = TenantScope::resolveTenantId();

        $query = DB::table('sales')
            ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('taxes', 'products.tax_id', '=', 'taxes.id')
            ->whereNotNull('products.tax_id')
            ->when($tenantId, fn ($q) => $q->where('sales.tenant_id', $tenantId))
            ->select(
                'taxes.id',
                'taxes.name as tax_name',
                DB::raw('COALESCE(SUM((sale_details.subtotal * taxes.rate) / 100), 0) as total_tax')
            );

        if (!empty($this->filters['from'])) {
            $query->whereDate('sales.sale_date', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('sales.sale_date', '<=', $this->filters['to']);
        }

        return $query
            ->groupBy('taxes.id', 'taxes.name')
            ->orderBy('taxes.name', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return ['Impuesto', 'Total recaudado'];
    }

    public function map($row): array
    {
        return [
            $row->tax_name,
            '$ ' . number_format((float) $row->total_tax, 2, ',', '.'),
        ];
    }

    public function registerEvents(): array
    {
        $settings   = $this->settings;
        $user       = $this->user;
        $headerRows = $this->headerRows;
        $lastCol    = 'B';

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

                // Anchos amplios para PDF A4 apaisado
                $sheet->getColumnDimension('A')->setWidth(50);
                $sheet->getColumnDimension('B')->setWidth(40);

                // Orientación horizontal para PDF
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
            },
        ];
    }
}
