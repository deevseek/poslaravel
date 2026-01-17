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
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\WarrantyClaimController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HrdSettingController;

Route::get('/', [TenantRegistrationController::class, 'landing'])->name('landing');
Route::post('tenant-registrations', [TenantRegistrationController::class, 'store'])
    ->middleware('central.domain')
    ->name('tenant-registrations.store');

Route::get('service-progress/{service}', [ServiceController::class, 'progress'])
    ->name('services.progress')
    ->middleware('signed');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware(['feature:dashboard', 'can:dashboard.view'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings', [SettingController::class, 'index'])
        ->middleware(['feature:settings', 'can:settings.view'])
        ->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])
        ->middleware(['feature:settings', 'can:settings.update'])
        ->name('settings.update');

    Route::post('customers/import', [CustomerController::class, 'importCsv'])
        ->middleware(['feature:customers', 'can:customer.manage'])
        ->name('customers.import');
    Route::resource('customers', CustomerController::class)
        ->middleware(['feature:customers', 'can:customer.manage']);
    Route::resource('suppliers', SupplierController::class)
        ->middleware(['feature:suppliers', 'can:supplier.manage']);

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('categories.index')->middleware(['feature:product', 'can:inventory.view']);
        Route::get('categories/create', 'create')->name('categories.create')->middleware(['feature:product', 'can:inventory.create']);
        Route::post('categories', 'store')->name('categories.store')->middleware(['feature:product', 'can:inventory.create']);
        Route::post('categories/import', 'importCsv')->name('categories.import')->middleware(['feature:product', 'can:inventory.create']);
        Route::get('categories/{category}', 'show')->name('categories.show')->middleware(['feature:product', 'can:inventory.view']);
        Route::get('categories/{category}/edit', 'edit')->name('categories.edit')->middleware(['feature:product', 'can:inventory.update']);
        Route::put('categories/{category}', 'update')->name('categories.update')->middleware(['feature:product', 'can:inventory.update']);
        Route::delete('categories/{category}', 'destroy')->name('categories.destroy')->middleware(['feature:product', 'can:inventory.update']);
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index')->name('products.index')->middleware(['feature:product', 'can:inventory.view']);
        Route::get('products/create', 'create')->name('products.create')->middleware(['feature:product', 'can:inventory.create']);
        Route::post('products', 'store')->name('products.store')->middleware(['feature:product', 'can:inventory.create']);
        Route::post('products/import', 'importCsv')->name('products.import')->middleware(['feature:product', 'can:inventory.create']);
        Route::get('products/{product}', 'show')->name('products.show')->middleware(['feature:product', 'can:inventory.view']);
        Route::get('products/{product}/edit', 'edit')->name('products.edit')->middleware(['feature:product', 'can:inventory.update']);
        Route::put('products/{product}', 'update')->name('products.update')->middleware(['feature:product', 'can:inventory.update']);
        Route::delete('products/{product}', 'destroy')->name('products.destroy')->middleware(['feature:product', 'can:inventory.delete']);
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('services', 'index')->name('services.index')->middleware(['feature:service', 'can:service.access']);
        Route::get('services/create', 'create')->name('services.create')->middleware(['feature:service', 'can:service.create']);
        Route::post('services', 'store')->name('services.store')->middleware(['feature:service', 'can:service.create']);
        Route::get('services/{service}', 'show')->name('services.show')->middleware(['feature:service', 'can:service.access']);
        Route::match(['put', 'patch'], 'services/{service}', 'update')
            ->name('services.update')
            ->middleware(['feature:service', 'can:service.create']);
        Route::post('services/{service}/status', 'updateStatus')->name('services.status')->middleware(['feature:service', 'can:service.update_status']);
        Route::post('services/{service}/items', 'addItem')->name('services.items.store')->middleware(['feature:service', 'can:service.add_sparepart']);
        Route::get('services/{service}/receipt', 'receipt')->name('services.receipt')->middleware(['feature:service', 'can:service.access']);
        Route::get('services/{service}/invoice', 'invoice')->name('services.invoice')->middleware(['feature:service', 'can:service.access']);
    });

    Route::controller(PurchaseController::class)->group(function () {
        Route::get('purchases', 'index')->name('purchases.index')->middleware(['feature:purchase', 'can:purchase.view']);
        Route::get('purchases/create', 'create')->name('purchases.create')->middleware(['feature:purchase', 'can:purchase.create']);
        Route::post('purchases', 'store')->name('purchases.store')->middleware(['feature:purchase', 'can:purchase.create']);
    });

    Route::get('stock-movements', [StockMovementController::class, 'index'])
        ->middleware(['feature:stock', 'can:inventory.view'])
        ->name('stock-movements.index');
    Route::post('stock-movements', [StockMovementController::class, 'store'])
        ->middleware(['feature:stock', 'can:inventory.adjust_stock'])
        ->name('stock-movements.store');

    Route::controller(FinanceController::class)->group(function () {
        Route::get('finances', 'index')->name('finances.index')->middleware(['feature:finance', 'can:finance.view']);
        Route::post('finances/income', 'storeIncome')->name('finances.income.store')->middleware(['feature:finance', 'can:finance.create_income']);
        Route::post('finances/expense', 'storeExpense')->name('finances.expense.store')->middleware(['feature:finance', 'can:finance.create_expense']);
        Route::post('finances/cash/open', 'openCash')->name('finances.cash.open')->middleware(['feature:finance', 'can:finance.create_income']);
        Route::post('finances/cash/close', 'closeCash')->name('finances.cash.close')->middleware(['feature:finance', 'can:finance.close_cash']);
        Route::get('finances/export', 'export')->name('finances.export')->middleware(['feature:finance', 'can:finance.report']);
    });

    Route::resource('employees', EmployeeController::class)
        ->middleware(['feature:hrd', 'can:hrd.manage']);
    Route::get('hrd/settings', [HrdSettingController::class, 'index'])
        ->middleware(['feature:hrd', 'can:hrd.manage'])
        ->name('hrd-settings.index');
    Route::post('hrd/settings', [HrdSettingController::class, 'update'])
        ->middleware(['feature:hrd', 'can:hrd.manage'])
        ->name('hrd-settings.update');
    Route::post('attendances/identify', [AttendanceController::class, 'identify'])
        ->middleware(['feature:hrd', 'can:hrd.manage'])
        ->name('attendances.identify');
    Route::resource('attendances', AttendanceController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->middleware(['feature:hrd', 'can:hrd.manage']);

    Route::resource('payrolls', PayrollController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->middleware(['feature:payroll', 'can:payroll.manage']);
    Route::get('payrolls/{payroll}/slip', [PayrollController::class, 'slip'])
        ->middleware(['feature:payroll', 'can:payroll.manage'])
        ->name('payrolls.slip');

    Route::get('warranties/reminders', [WarrantyController::class, 'reminder'])
        ->middleware(['feature:warranty', 'can:warranty.view'])
        ->name('warranties.reminder');
    Route::controller(WarrantyController::class)->group(function () {
        Route::get('warranties', 'index')->name('warranties.index')->middleware(['feature:warranty', 'can:warranty.view']);
        Route::get('warranties/create', 'create')->name('warranties.create')->middleware(['feature:warranty', 'can:warranty.claim']);
        Route::post('warranties', 'store')->name('warranties.store')->middleware(['feature:warranty', 'can:warranty.claim']);
        Route::get('warranties/{warranty}', 'show')->name('warranties.show')->middleware(['feature:warranty', 'can:warranty.view']);
        Route::get('warranties/{warranty}/edit', 'edit')->name('warranties.edit')->middleware(['feature:warranty', 'can:warranty.approve']);
        Route::put('warranties/{warranty}', 'update')->name('warranties.update')->middleware(['feature:warranty', 'can:warranty.approve']);
        Route::delete('warranties/{warranty}', 'destroy')->name('warranties.destroy')->middleware(['feature:warranty', 'can:warranty.approve']);
    });
    Route::post('warranties/{warranty}/claims', [WarrantyClaimController::class, 'store'])
        ->middleware(['feature:warranty', 'can:warranty.claim'])
        ->name('warranty-claims.store');
    Route::put('warranty-claims/{warrantyClaim}', [WarrantyClaimController::class, 'update'])
        ->middleware(['feature:warranty', 'can:warranty.approve'])
        ->name('warranty-claims.update');

    Route::controller(PosController::class)->group(function () {
        Route::get('pos', 'index')->name('pos.index')->middleware(['feature:pos', 'can:pos.access']);
        Route::post('pos/cart', 'addToCart')->name('pos.cart.add')->middleware(['feature:pos', 'can:pos.create']);
        Route::patch('pos/cart/{product}', 'updateCart')->name('pos.cart.update')->middleware(['feature:pos', 'can:pos.create']);
        Route::delete('pos/cart/{product}', 'removeFromCart')->name('pos.cart.remove')->middleware(['feature:pos', 'can:pos.create']);
        Route::post('pos/checkout', 'checkout')->name('pos.checkout')->middleware(['feature:pos', 'can:pos.create']);
        Route::get('pos/transactions/{transaction}/receipt', 'receipt')->name('pos.receipt')->middleware(['feature:pos', 'can:pos.print']);
    });

    Route::middleware(['central.domain', 'feature:tenants', 'can:tenant.manage'])->group(function () {
        Route::resource('tenants', TenantController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::post('tenants/sync-migrations', [TenantController::class, 'syncMigrations'])
            ->name('tenants.sync-migrations');
        Route::post('tenants/sync-roles', [TenantController::class, 'syncRoles'])
            ->name('tenants.sync-roles');

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
        ->middleware(['feature:roles', 'can:role.manage']);
    Route::resource('users', UserController::class)
        ->middleware(['feature:users', 'can:user.manage']);
});

require base_path('app/Modules/Attendance/routes.php');

require __DIR__.'/auth.php';
