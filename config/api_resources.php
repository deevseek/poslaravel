<?php

use App\Models\Attendance;
use App\Models\CashSession;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Finance;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\ServiceLog;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Warranty;
use App\Models\WarrantyClaim;
use App\Modules\Attendance\Models\Attendance as AttendanceLog;

return [
    'resources' => [
        'attendance-logs' => [
            'model' => AttendanceLog::class,
            'searchable' => ['user_id', 'type', 'device_info'],
        ],
        'attendances' => [
            'model' => Attendance::class,
            'searchable' => ['employee_id', 'attendance_date', 'status', 'method'],
        ],
        'cash-sessions' => [
            'model' => CashSession::class,
            'searchable' => ['status', 'opened_by', 'closed_by'],
        ],
        'categories' => [
            'model' => Category::class,
            'searchable' => ['name'],
        ],
        'customers' => [
            'model' => Customer::class,
            'searchable' => ['name', 'email', 'phone'],
        ],
        'employees' => [
            'model' => Employee::class,
            'searchable' => ['name', 'email', 'phone'],
        ],
        'finances' => [
            'model' => Finance::class,
            'searchable' => ['type', 'description'],
        ],
        'payrolls' => [
            'model' => Payroll::class,
            'searchable' => ['employee_id', 'status'],
        ],
        'permissions' => [
            'model' => Permission::class,
            'searchable' => ['name', 'slug'],
        ],
        'products' => [
            'model' => Product::class,
            'searchable' => ['name', 'sku'],
        ],
        'purchases' => [
            'model' => Purchase::class,
            'searchable' => ['invoice_number', 'supplier_id'],
        ],
        'purchase-items' => [
            'model' => PurchaseItem::class,
            'searchable' => ['purchase_id', 'product_id'],
        ],
        'roles' => [
            'model' => Role::class,
            'searchable' => ['name', 'slug'],
        ],
        'services' => [
            'model' => Service::class,
            'searchable' => ['customer_name', 'status', 'service_type'],
        ],
        'service-items' => [
            'model' => ServiceItem::class,
            'searchable' => ['service_id', 'product_id'],
        ],
        'service-logs' => [
            'model' => ServiceLog::class,
            'searchable' => ['service_id', 'status'],
        ],
        'settings' => [
            'model' => Setting::class,
            'searchable' => ['key', 'group'],
        ],
        'stock-movements' => [
            'model' => StockMovement::class,
            'searchable' => ['reference', 'type'],
        ],
        'suppliers' => [
            'model' => Supplier::class,
            'searchable' => ['name', 'email', 'phone'],
        ],
        'transactions' => [
            'model' => Transaction::class,
            'searchable' => ['invoice_number', 'customer_id', 'status'],
        ],
        'transaction-items' => [
            'model' => TransactionItem::class,
            'searchable' => ['transaction_id', 'product_id'],
        ],
        'users' => [
            'model' => User::class,
            'searchable' => ['name', 'email'],
        ],
        'warranties' => [
            'model' => Warranty::class,
            'searchable' => ['customer_name', 'status'],
        ],
        'warranty-claims' => [
            'model' => WarrantyClaim::class,
            'searchable' => ['warranty_id', 'status'],
        ],
    ],
];
