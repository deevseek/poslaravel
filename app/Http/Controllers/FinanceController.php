<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->input('month', now()->format('Y-m'));

        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
            $month = $start->format('Y-m');
        }

        $end = (clone $start)->endOfMonth();

        $finances = Finance::with('cashSession')
            ->whereBetween('recorded_at', [$start, $end])
            ->orderByDesc('recorded_at')
            ->latest()
            ->paginate(15);

        $incomeTotal = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'income')
            ->sum('nominal');

        $expenseTotal = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'expense')
            ->sum('nominal');

        $omsetPos = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'income')
            ->where('category', 'Penjualan')
            ->where('source', 'pos')
            ->sum('nominal');

        $omsetService = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'income')
            ->where('category', 'Penjualan')
            ->where('source', 'service')
            ->sum('nominal');

        $totalIncome = $omsetPos + $omsetService;

        $hppPos = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'expense')
            ->where('category', 'HPP')
            ->where('source', 'pos')
            ->sum('nominal');

        $hppService = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'expense')
            ->where('category', 'HPP')
            ->where('source', 'service')
            ->sum('nominal');

        $totalHpp = $hppPos + $hppService;

        $operationalExpense = Finance::whereBetween('recorded_at', [$start, $end])
            ->where('type', 'expense')
            ->where('source', 'manual')
            ->where('category', '!=', 'HPP')
            ->sum('nominal');

        $grossProfit = ($omsetPos + $omsetService) - ($hppPos + $hppService);
        $netProfit = $grossProfit - $operationalExpense;

        $today = now()->toDateString();
        $todayIncome = Finance::whereDate('recorded_at', $today)->where('type', 'income')->sum('nominal');
        $todayExpense = Finance::whereDate('recorded_at', $today)->where('type', 'expense')->sum('nominal');

        $activeSession = CashSession::active()->latest('opened_at')->first();
        $recentSessions = CashSession::orderByDesc('opened_at')->take(5)->get();

        return view('finances.index', [
            'finances' => $finances,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'posIncome' => $omsetPos,
            'serviceIncome' => $omsetService,
            'omset_pos' => $omsetPos,
            'omset_service' => $omsetService,
            'total_income' => $totalIncome,
            'posHpp' => $hppPos,
            'serviceHpp' => $hppService,
            'hpp_pos' => $hppPos,
            'hpp_service' => $hppService,
            'total_hpp' => $totalHpp,
            'total_expense' => $operationalExpense,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'todayIncome' => $todayIncome,
            'todayExpense' => $todayExpense,
            'month' => $month,
            'activeSession' => $activeSession,
            'recentSessions' => $recentSessions,
        ]);
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        return $this->storeTransaction($request, 'income');
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        return $this->storeTransaction($request, 'expense');
    }

    protected function storeTransaction(Request $request, string $type): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
            'recorded_at' => ['required', 'date'],
        ]);

        $session = CashSession::active()->latest('opened_at')->first();

        Finance::create([
            'cash_session_id' => $session?->id,
            'type' => $type,
            'category' => $validated['category'],
            'nominal' => $validated['nominal'],
            'note' => $validated['note'] ?? null,
            'recorded_at' => $validated['recorded_at'],
        ]);

        return redirect()->route('finances.index')->with('success', 'Transaksi keuangan berhasil dicatat.');
    }

    public function openCash(Request $request): RedirectResponse
    {
        $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if (CashSession::active()->exists()) {
            return redirect()->route('finances.index')->with('error', 'Masih ada kas yang belum ditutup.');
        }

        CashSession::create([
            'opening_balance' => $request->input('opening_balance'),
            'note' => $request->input('note'),
            'opened_at' => now(),
        ]);

        return redirect()->route('finances.index')->with('success', 'Kas harian berhasil dibuka.');
    }

    public function closeCash(Request $request): RedirectResponse
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $session = CashSession::active()->latest('opened_at')->first();

        if (!$session) {
            return redirect()->route('finances.index')->with('error', 'Tidak ada kas yang sedang dibuka.');
        }

        $income = $session->finances()->where('type', 'income')->sum('nominal');
        $expense = $session->finances()->where('type', 'expense')->sum('nominal');
        $closing = $session->opening_balance + $income - $expense;

        $session->update([
            'closing_balance' => $closing,
            'note' => $request->input('note'),
            'closed_at' => now(),
        ]);

        return redirect()->route('finances.index')->with('success', 'Kas harian berhasil ditutup.');
    }

    public function export(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
        }

        $end = (clone $start)->endOfMonth();

        $finances = Finance::whereBetween('recorded_at', [$start, $end])
            ->orderBy('recorded_at')
            ->get();

        $filename = 'laporan-keuangan-' . $start->format('Y-m') . '.csv';

        return Response::streamDownload(function () use ($finances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'Jenis', 'Kategori', 'Nominal', 'Catatan']);

            foreach ($finances as $finance) {
                fputcsv($handle, [
                    $finance->recorded_at->format('Y-m-d'),
                    $finance->type,
                    $finance->category,
                    $finance->nominal,
                    $finance->note,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
