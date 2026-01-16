<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Finance;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Service;
use App\Models\Transaction;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $today = now();
        $days = max(1, $request->integer('days', 7));

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

        $financeStart = now()->subDays($days - 1)->startOfDay();
        $financeEnd = now()->endOfDay();

        $financeSummary = Finance::selectRaw('DATE(recorded_at) as date')
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN nominal ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN nominal ELSE 0 END) as expense")
            ->whereBetween('recorded_at', [$financeStart, $financeEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $financeChartLabels = [];
        $financeChartIncome = [];
        $financeChartExpense = [];

        foreach (CarbonPeriod::create($financeStart, '1 day', $financeEnd) as $date) {
            $formattedDate = $date->format('Y-m-d');

            $financeChartLabels[] = $date->format('d M');
            $financeChartIncome[] = (float) ($financeSummary[$formattedDate]->income ?? 0);
            $financeChartExpense[] = (float) ($financeSummary[$formattedDate]->expense ?? 0);
        }

        return response()->json([
            'data' => [
                'today_sales' => $todaySales,
                'monthly_sales' => $monthlySales,
                'transactions_today' => $transactionsToday,
                'customers_count' => $customersCount,
                'products_count' => $productsCount,
                'active_services_count' => $activeServicesCount,
                'outstanding_purchases' => $outstandingPurchases,
                'recent_transactions' => $recentTransactions,
                'recent_services' => $recentServices,
                'finance_chart' => [
                    'labels' => $financeChartLabels,
                    'income' => $financeChartIncome,
                    'expense' => $financeChartExpense,
                ],
            ],
        ]);
    }
}
