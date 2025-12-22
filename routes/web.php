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
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\WarrantyClaimController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WaBroadcastController;
use App\Http\Controllers\WaTemplateController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings', [SettingController::class, 'index'])
        ->middleware('permission:settings.view')
        ->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])
        ->middleware('permission:settings.update')
        ->name('settings.update');

    Route::resource('customers', CustomerController::class)->middleware('permission:customer.manage');
    Route::resource('suppliers', SupplierController::class)->middleware('permission:supplier.manage');

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('categories.index')->middleware('permission:inventory.view');
        Route::get('categories/create', 'create')->name('categories.create')->middleware('permission:inventory.update');
        Route::post('categories', 'store')->name('categories.store')->middleware('permission:inventory.update');
        Route::get('categories/{category}', 'show')->name('categories.show')->middleware('permission:inventory.view');
        Route::get('categories/{category}/edit', 'edit')->name('categories.edit')->middleware('permission:inventory.update');
        Route::put('categories/{category}', 'update')->name('categories.update')->middleware('permission:inventory.update');
        Route::delete('categories/{category}', 'destroy')->name('categories.destroy')->middleware('permission:inventory.update');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index')->name('products.index')->middleware('permission:inventory.view');
        Route::get('products/create', 'create')->name('products.create')->middleware('permission:inventory.create');
        Route::post('products', 'store')->name('products.store')->middleware('permission:inventory.create');
        Route::get('products/{product}', 'show')->name('products.show')->middleware('permission:inventory.view');
        Route::get('products/{product}/edit', 'edit')->name('products.edit')->middleware('permission:inventory.update');
        Route::put('products/{product}', 'update')->name('products.update')->middleware('permission:inventory.update');
        Route::delete('products/{product}', 'destroy')->name('products.destroy')->middleware('permission:inventory.update');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('services', 'index')->name('services.index')->middleware('permission:service.access');
        Route::get('services/create', 'create')->name('services.create')->middleware('permission:service.create');
        Route::post('services', 'store')->name('services.store')->middleware('permission:service.create');
        Route::get('services/{service}', 'show')->name('services.show')->middleware('permission:service.access');
        Route::put('services/{service}', 'update')->name('services.update')->middleware('permission:service.create');
        Route::patch('services/{service}', 'update')->name('services.update')->middleware('permission:service.create');
        Route::post('services/{service}/status', 'updateStatus')->name('services.status')->middleware('permission:service.update_status');
        Route::post('services/{service}/items', 'addItem')->name('services.items.store')->middleware('permission:service.add_sparepart');
        Route::get('services/{service}/receipt', 'receipt')->name('services.receipt')->middleware('permission:service.access');
        Route::get('services/{service}/invoice', 'invoice')->name('services.invoice')->middleware('permission:service.access');
    });

    Route::controller(PurchaseController::class)->group(function () {
        Route::get('purchases', 'index')->name('purchases.index')->middleware('permission:purchase.view');
        Route::get('purchases/create', 'create')->name('purchases.create')->middleware('permission:purchase.create');
        Route::post('purchases', 'store')->name('purchases.store')->middleware('permission:purchase.create');
    });

    Route::get('stock-movements', [StockMovementController::class, 'index'])
        ->middleware('permission:inventory.view')
        ->name('stock-movements.index');
    Route::post('stock-movements', [StockMovementController::class, 'store'])
        ->middleware('permission:inventory.adjust_stock')
        ->name('stock-movements.store');

    Route::controller(FinanceController::class)->group(function () {
        Route::get('finances', 'index')->name('finances.index')->middleware('permission:finance.view');
        Route::post('finances/income', 'storeIncome')->name('finances.income.store')->middleware('permission:finance.create_income');
        Route::post('finances/expense', 'storeExpense')->name('finances.expense.store')->middleware('permission:finance.create_expense');
        Route::post('finances/cash/open', 'openCash')->name('finances.cash.open')->middleware('permission:finance.create_income');
        Route::post('finances/cash/close', 'closeCash')->name('finances.cash.close')->middleware('permission:finance.close_cash');
        Route::get('finances/export', 'export')->name('finances.export')->middleware('permission:finance.report');
    });

    Route::get('warranties/reminders', [WarrantyController::class, 'reminder'])
        ->middleware('permission:warranty.view')
        ->name('warranties.reminder');
    Route::controller(WarrantyController::class)->group(function () {
        Route::get('warranties', 'index')->name('warranties.index')->middleware('permission:warranty.view');
        Route::get('warranties/create', 'create')->name('warranties.create')->middleware('permission:warranty.claim');
        Route::post('warranties', 'store')->name('warranties.store')->middleware('permission:warranty.claim');
        Route::get('warranties/{warranty}', 'show')->name('warranties.show')->middleware('permission:warranty.view');
        Route::get('warranties/{warranty}/edit', 'edit')->name('warranties.edit')->middleware('permission:warranty.approve');
        Route::put('warranties/{warranty}', 'update')->name('warranties.update')->middleware('permission:warranty.approve');
        Route::delete('warranties/{warranty}', 'destroy')->name('warranties.destroy')->middleware('permission:warranty.approve');
    });
    Route::post('warranties/{warranty}/claims', [WarrantyClaimController::class, 'store'])
        ->middleware('permission:warranty.claim')
        ->name('warranty-claims.store');
    Route::put('warranty-claims/{warrantyClaim}', [WarrantyClaimController::class, 'update'])
        ->middleware('permission:warranty.approve')
        ->name('warranty-claims.update');

    Route::controller(PosController::class)->group(function () {
        Route::get('pos', 'index')->name('pos.index')->middleware('permission:pos.access');
        Route::post('pos/cart', 'addToCart')->name('pos.cart.add')->middleware('permission:pos.create');
        Route::patch('pos/cart/{product}', 'updateCart')->name('pos.cart.update')->middleware('permission:pos.create');
        Route::delete('pos/cart/{product}', 'removeFromCart')->name('pos.cart.remove')->middleware('permission:pos.create');
        Route::post('pos/checkout', 'checkout')->name('pos.checkout')->middleware('permission:pos.create');
        Route::get('pos/transactions/{transaction}/receipt', 'receipt')->name('pos.receipt')->middleware('permission:pos.print');
    });

    Route::resource('wa-templates', WaTemplateController::class)->middleware('permission:whatsapp.template_manage');
    Route::get('wa/broadcast', [WaBroadcastController::class, 'index'])
        ->middleware('permission:whatsapp.broadcast')
        ->name('wa.broadcast');
    Route::post('wa/broadcast', [WaBroadcastController::class, 'send'])
        ->middleware('permission:whatsapp.broadcast')
        ->name('wa.broadcast.send');
    Route::get('wa/logs', [WaBroadcastController::class, 'logs'])
        ->middleware('permission:whatsapp.log_view')
        ->name('wa.logs');

    Route::resource('tenants', TenantController::class)
        ->only(['index', 'create', 'store', 'edit', 'update'])
        ->middleware('permission:tenant.manage');

    Route::resource('roles', RoleController::class)
        ->middleware('permission:role.manage');
    Route::resource('users', UserController::class)
        ->middleware('permission:user.manage');
});

require __DIR__.'/auth.php';
