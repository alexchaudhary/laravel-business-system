<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockAdjustmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);

    Route::resource('products', ProductController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('purchases', PurchaseController::class);

    Route::resource('sales', SaleController::class);

    Route::get('/inventory', [InventoryController::class, 'index'])
    ->name('inventory.index');

    Route::resource('stock-adjustments', StockAdjustmentController::class)
    ->only(['index', 'create', 'store']);

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';