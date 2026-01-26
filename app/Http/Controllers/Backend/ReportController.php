<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ReportsRepository;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\ProductsExport;
use App\Exports\SalesExport;
use App\Exports\TaxesExport;

use App\Models\Product;
use App\Models\Tax;

class ReportController extends Controller
{
    protected $reports;

    public function __construct(ReportsRepository $reports)
    {
        $this->reports = $reports;
    }

    /**
     * Obtener el reporte de productos
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
    */
    public function products(Request $request) {   

        $data = $this->reports->getProductsReport($request->all());
        
        return response()->json($data);
    }

    /**
     * Obtener el reporte de ventas
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
    */
    public function sales(Request $request) {

        $data = $this->reports->getSalesReport($request->all());

        return response()->json($data);

    }

    /**
     * Obtener el reporte de impuestos aplicados
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
    */
    public function taxes(Request $request) {

        $data = $this->reports->getTaxesReport($request->all());

        return response()->json($data);

    }

    /**
     * Exportar el reporte de productos
     * 
     * @param Request $request
     * @param string $format
     * @return \Illuminate\Http\Response
     * 
    */
   public function exportProducts(Request $request, $format) {

        $filters = $request->all();
        $export = new ProductsExport($filters);

        if ($format === 'excel') {

            return Excel::download($export, 'reporte_productos.xlsx');

        } elseif ($format === 'pdf') {

            return Excel::download($export, 'reporte_productos.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        
        } else {
            abort(404);
        }

    }

    /**
     * Exportar el reporte de ventas
     * 
     * @param Request $request
     * @param string $format
     * @return \Illuminate\Http\Response
     * 
    */
   public function exportSales(Request $request, $format) {

       $filters = $request->all();
       $export = new SalesExport($filters);

       if ($format === 'excel') {

           return Excel::download($export, 'reporte_ventas.xlsx');

       } elseif ($format === 'pdf') {

           return Excel::download($export, 'reporte_ventas.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

       } else {
           abort(404);
       }

   }

   /**
    * Exportar el reporte de impuestos
    * 
    * @param Request $request
    * @param string $format
    * @return \Illuminate\Http\Response
    * 
   */
   public function exportTaxes(Request $request, $format) {

       $filters = $request->all();
       $export = new TaxesExport($filters);

       if ($format === 'excel') {

           return Excel::download($export, 'reporte_impuestos.xlsx');

       } elseif ($format === 'pdf') {

           return Excel::download($export, 'reporte_impuestos.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

       } else {
           abort(404);
       }

   }


}