<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ServiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('services', ServiceController::class);
    Route::post('services/{service}/status', [ServiceController::class, 'updateStatus'])->name('services.status');
    Route::post('services/{service}/items', [ServiceController::class, 'addItem'])->name('services.items.store');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'store']);

    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::post('stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');

    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/cart', [PosController::class, 'addToCart'])->name('pos.cart.add');
    Route::patch('pos/cart/{product}', [PosController::class, 'updateCart'])->name('pos.cart.update');
    Route::delete('pos/cart/{product}', [PosController::class, 'removeFromCart'])->name('pos.cart.remove');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('pos/transactions/{transaction}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
});

require __DIR__.'/auth.php';
