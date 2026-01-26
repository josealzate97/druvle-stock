<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\UserController; 
use App\Http\Controllers\Backend\CategoryController; 
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SaleController;
use App\Http\Controllers\Backend\TaxController;
use App\Http\Controllers\Backend\ReportController;

/*
 * ✅ Rutas para landing publica
*/
Route::get('/', function () {
    return view('backend.auth.login');
});


/**
 *  ✅ Rutas para autenticacion
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {

     Route::get('home', [DefaultController::class, 'dashboard'])->name('home');

    /*
     * ✅ Rutas para categorias
    */
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('categories/getCategory/{id}', [CategoryController::class, 'getCategory'])->name('categories.getCategory');
    Route::post('categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('categories/delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
    Route::post('categories/activate/{id}', [CategoryController::class, 'activate'])->name('categories.activate');

    /*
     * ✅ Rutas para productos
    */
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::post('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('products/getProduct/{id}', [ProductController::class, 'getProduct'])->name('products.getProduct');
    Route::get('products/getActiveProducts', [ProductController::class, 'getActiveProducts'])->name('products.getActiveProducts');
    Route::post('products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::post('products/delete/{id}', [ProductController::class, 'delete'])->name('products.delete');
    Route::post('products/activate/{id}', [ProductController::class, 'activate'])->name('products.activate');

    /*
     * ✅ Rutas para ventas
    */
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('sales/save', [SaleController::class, 'save'])->name('sales.save');
    Route::get('/sales/detail/{id}', [SaleController::class, 'detail']);

    Route::get('/sales/invoice/{id}', [SaleController::class, 'invoice']);
    Route::get('/sales/refund-form/{id}', [SaleController::class, 'refundForm'])->name('sales.refundForm');
    Route::post('/sales/refund/{id}', [SaleController::class, 'processRefund']);

    Route::post('/sales/send-email/{id}', [SaleController::class, 'sendEmail']);

    /*
     * ✅ Rutas para usuarios
    */
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/info/{id}', [UserController::class, 'info'])->name('users.info');
    Route::post('users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('users/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
    Route::post('users/activate/{id}', [UserController::class, 'activate'])->name('users.activate');


    /*
     * ✅ Rutas para reportes
    */
    Route::get('reports', function () {

         return view('backend.reports.index');
        
    })->name('reports.index');

    
    Route::get('reports/products', [ReportController::class, 'products']);
    Route::get('reports/sales', [ReportController::class, 'sales']);
    Route::get('reports/taxes', [ReportController::class, 'taxes']);

    // Para exportar
    Route::get('reports/products/export/{format}', [ReportController::class, 'exportProducts']);
    Route::get('reports/sales/export/{format}', [ReportController::class, 'exportSales']);
    Route::get('reports/taxes/export/{format}', [ReportController::class, 'exportTaxes']);


    /*
     * ✅ Rutas para configuraciones
    */
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/update/{id}', [SettingController::class, 'update'])->name('settings.update');


    /*
     * ✅ Rutas para impuestos
    */
    Route::post('/taxes/update/{id}', [TaxController::class, 'update'])->name('taxes.update');
    Route::post('/taxes/delete/{id}', [TaxController::class, 'delete'])->name('taxes.delete');
    Route::post('/taxes/activate/{id}', [TaxController::class, 'activate'])->name('taxes.activate');
    Route::post('/taxes/create', [TaxController::class, 'create'])->name('taxes.create');
    
});



