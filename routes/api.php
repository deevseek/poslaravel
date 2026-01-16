<?php

use App\Http\Controllers\Api\ApiResourceController;
use App\Http\Controllers\Api\AuthController;
use App\Modules\Attendance\Controllers\AttendanceController as AttendanceModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API v1 is available.',
        ]);
    });

    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('api.token')->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::post('attendance/check-in', [AttendanceModuleController::class, 'checkIn']);
        Route::post('attendance/check-out', [AttendanceModuleController::class, 'checkOut']);

        Route::name('api.')->group(function (): void {
            Route::apiResource('attendance-logs', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('attendances', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('cash-sessions', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('categories', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('customers', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('employees', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('finances', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('payrolls', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('permissions', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('products', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('purchases', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('purchase-items', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('roles', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('services', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('service-items', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('service-logs', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('settings', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('stock-movements', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('suppliers', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('transactions', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('transaction-items', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('users', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('warranties', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('warranty-claims', ApiResourceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        });
    });
});
