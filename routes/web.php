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
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::get('/', [TenantRegistrationController::class, 'landing'])->name('landing');
Route::post('tenant-registrations', [TenantRegistrationController::class, 'store'])
    ->middleware('central.domain')
    ->name('tenant-registrations.store');

Route::get('service-progress/{service}', [ServiceController::class, 'progress'])
    ->name('services.progress')
    ->middleware('signed');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('can:dashboard.view')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings', [SettingController::class, 'index'])
        ->middleware('can:settings.view')
        ->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])
        ->middleware('can:settings.update')
        ->name('settings.update');

    Route::resource('customers', CustomerController::class)->middleware('can:customer.manage');
    Route::resource('suppliers', SupplierController::class)->middleware('can:supplier.manage');

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('categories.index')->middleware('can:inventory.view');
        Route::get('categories/create', 'create')->name('categories.create')->middleware('can:inventory.update');
        Route::post('categories', 'store')->name('categories.store')->middleware('can:inventory.update');
        Route::get('categories/{category}', 'show')->name('categories.show')->middleware('can:inventory.view');
        Route::get('categories/{category}/edit', 'edit')->name('categories.edit')->middleware('can:inventory.update');
        Route::put('categories/{category}', 'update')->name('categories.update')->middleware('can:inventory.update');
        Route::delete('categories/{category}', 'destroy')->name('categories.destroy')->middleware('can:inventory.update');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index')->name('products.index')->middleware('can:inventory.view');
        Route::get('products/create', 'create')->name('products.create')->middleware('can:inventory.create');
        Route::post('products', 'store')->name('products.store')->middleware('can:inventory.create');
        Route::get('products/{product}', 'show')->name('products.show')->middleware('can:inventory.view');
        Route::get('products/{product}/edit', 'edit')->name('products.edit')->middleware('can:inventory.update');
        Route::put('products/{product}', 'update')->name('products.update')->middleware('can:inventory.update');
        Route::delete('products/{product}', 'destroy')->name('products.destroy')->middleware('can:inventory.update');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('services', 'index')->name('services.index')->middleware('can:service.access');
        Route::get('services/create', 'create')->name('services.create')->middleware('can:service.create');
        Route::post('services', 'store')->name('services.store')->middleware('can:service.create');
        Route::get('services/{service}', 'show')->name('services.show')->middleware('can:service.access');
        Route::match(['put', 'patch'], 'services/{service}', 'update')
            ->name('services.update')
            ->middleware('can:service.create');
        Route::post('services/{service}/status', 'updateStatus')->name('services.status')->middleware('can:service.update_status');
        Route::post('services/{service}/items', 'addItem')->name('services.items.store')->middleware('can:service.add_sparepart');
        Route::get('services/{service}/receipt', 'receipt')->name('services.receipt')->middleware('can:service.access');
        Route::get('services/{service}/invoice', 'invoice')->name('services.invoice')->middleware('can:service.access');
    });

    Route::controller(PurchaseController::class)->group(function () {
        Route::get('purchases', 'index')->name('purchases.index')->middleware('can:purchase.view');
        Route::get('purchases/create', 'create')->name('purchases.create')->middleware('can:purchase.create');
        Route::post('purchases', 'store')->name('purchases.store')->middleware('can:purchase.create');
    });

    Route::get('stock-movements', [StockMovementController::class, 'index'])
        ->middleware('can:inventory.view')
        ->name('stock-movements.index');
    Route::post('stock-movements', [StockMovementController::class, 'store'])
        ->middleware('can:inventory.adjust_stock')
        ->name('stock-movements.store');

    Route::controller(FinanceController::class)->group(function () {
        Route::get('finances', 'index')->name('finances.index')->middleware('can:finance.view');
        Route::post('finances/income', 'storeIncome')->name('finances.income.store')->middleware('can:finance.create_income');
        Route::post('finances/expense', 'storeExpense')->name('finances.expense.store')->middleware('can:finance.create_expense');
        Route::post('finances/cash/open', 'openCash')->name('finances.cash.open')->middleware('can:finance.create_income');
        Route::post('finances/cash/close', 'closeCash')->name('finances.cash.close')->middleware('can:finance.close_cash');
        Route::get('finances/export', 'export')->name('finances.export')->middleware('can:finance.report');
    });

    Route::get('warranties/reminders', [WarrantyController::class, 'reminder'])
        ->middleware('can:warranty.view')
        ->name('warranties.reminder');
    Route::controller(WarrantyController::class)->group(function () {
        Route::get('warranties', 'index')->name('warranties.index')->middleware('can:warranty.view');
        Route::get('warranties/create', 'create')->name('warranties.create')->middleware('can:warranty.claim');
        Route::post('warranties', 'store')->name('warranties.store')->middleware('can:warranty.claim');
        Route::get('warranties/{warranty}', 'show')->name('warranties.show')->middleware('can:warranty.view');
        Route::get('warranties/{warranty}/edit', 'edit')->name('warranties.edit')->middleware('can:warranty.approve');
        Route::put('warranties/{warranty}', 'update')->name('warranties.update')->middleware('can:warranty.approve');
        Route::delete('warranties/{warranty}', 'destroy')->name('warranties.destroy')->middleware('can:warranty.approve');
    });
    Route::post('warranties/{warranty}/claims', [WarrantyClaimController::class, 'store'])
        ->middleware('can:warranty.claim')
        ->name('warranty-claims.store');
    Route::put('warranty-claims/{warrantyClaim}', [WarrantyClaimController::class, 'update'])
        ->middleware('can:warranty.approve')
        ->name('warranty-claims.update');

    Route::controller(PosController::class)->group(function () {
        Route::get('pos', 'index')->name('pos.index')->middleware('can:pos.access');
        Route::post('pos/cart', 'addToCart')->name('pos.cart.add')->middleware('can:pos.create');
        Route::patch('pos/cart/{product}', 'updateCart')->name('pos.cart.update')->middleware('can:pos.create');
        Route::delete('pos/cart/{product}', 'removeFromCart')->name('pos.cart.remove')->middleware('can:pos.create');
        Route::post('pos/checkout', 'checkout')->name('pos.checkout')->middleware('can:pos.create');
        Route::get('pos/transactions/{transaction}/receipt', 'receipt')->name('pos.receipt')->middleware('can:pos.print');
    });

    Route::resource('wa-templates', WaTemplateController::class)->middleware('can:whatsapp.template_manage');
    Route::get('wa/broadcast', [WaBroadcastController::class, 'index'])
        ->middleware('can:whatsapp.broadcast')
        ->name('wa.broadcast');
    Route::post('wa/broadcast', [WaBroadcastController::class, 'send'])
        ->middleware('can:whatsapp.broadcast')
        ->name('wa.broadcast.send');
    Route::get('wa/logs', [WaBroadcastController::class, 'logs'])
        ->middleware('can:whatsapp.log_view')
        ->name('wa.logs');

    Route::middleware(['central.domain', 'can:tenant.manage'])->group(function () {
        Route::resource('tenants', TenantController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::get('tenant-registrations', [TenantRegistrationController::class, 'index'])
            ->name('tenant-registrations.index');
        Route::post('tenant-registrations/{tenantRegistration}/approve', [TenantRegistrationController::class, 'approve'])
            ->name('tenant-registrations.approve');
        Route::post('tenant-registrations/{tenantRegistration}/reject', [TenantRegistrationController::class, 'reject'])
            ->name('tenant-registrations.reject');

        Route::resource('subscription-plans', SubscriptionPlanController::class)
            ->except(['show']);
    });

    Route::resource('roles', RoleController::class)
        ->middleware('can:role.manage');
    Route::resource('users', UserController::class)
        ->middleware('can:user.manage');
});

require __DIR__.'/auth.php';
