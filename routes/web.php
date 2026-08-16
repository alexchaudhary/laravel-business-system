<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    Route::resource('customers', CustomerController::class);


    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::resource('products', ProductController::class);


    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */

    Route::resource('suppliers', SupplierController::class);


    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    Route::resource('purchases', PurchaseController::class);


    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    */

    Route::resource('sales', SaleController::class);

/*
|--------------------------------------------------------------------------
| Expenses
|--------------------------------------------------------------------------
*/

Route::resource('expenses', ExpenseController::class);

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');


    /*
    |--------------------------------------------------------------------------
    | Stock Adjustments
    |--------------------------------------------------------------------------
    |
    | Open adjustment form for a specific product
    |
    */

    Route::get(
        '/inventory/{product}/adjust-stock',
        [StockAdjustmentController::class, 'create']
    )->name('stock-adjustments.create');


    /*
    |--------------------------------------------------------------------------
    | Save Stock Adjustment
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/inventory/{product}/adjust-stock',
        [StockAdjustmentController::class, 'store']
    )->name('stock-adjustments.store');


    /*
    |--------------------------------------------------------------------------
    | Stock Adjustment History
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/stock-adjustments',
        [StockAdjustmentController::class, 'index']
    )->name('stock-adjustments.index');

    Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';