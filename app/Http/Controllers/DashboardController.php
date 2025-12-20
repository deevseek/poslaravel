<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now();

        $todaySales = Transaction::whereDate('created_at', $today)->sum('total');
        $monthlySales = Transaction::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->sum('total');
        $transactionsToday = Transaction::whereDate('created_at', $today)->count();

        $customersCount = Customer::count();
        $productsCount = Product::count();
        $activeServicesCount = Service::where('status', '!=', Service::STATUS_DIAMBIL)->count();
        $outstandingPurchases = Purchase::where('payment_status', Purchase::STATUS_DEBT)->sum('total_amount');

        $recentTransactions = Transaction::with('customer')->latest()->take(5)->get();
        $recentServices = Service::with('customer')->latest()->take(5)->get();

        return view('dashboard', [
            'todaySales' => $todaySales,
            'monthlySales' => $monthlySales,
            'transactionsToday' => $transactionsToday,
            'customersCount' => $customersCount,
            'productsCount' => $productsCount,
            'activeServicesCount' => $activeServicesCount,
            'outstandingPurchases' => $outstandingPurchases,
            'recentTransactions' => $recentTransactions,
            'recentServices' => $recentServices,
        ]);
    }
}
