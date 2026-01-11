<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use App\Models\Employee;
use App\Models\Finance;
use App\Models\Payroll;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(): View
    {
        $payrolls = Payroll::with('employee')
            ->orderByDesc('pay_date')
            ->latest()
            ->paginate(10);

        return view('payrolls.index', compact('payrolls'));
    }

    public function create(): View
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $selectedEmployeeId = request('employee_id');

        return view('payrolls.create', compact('employees', 'selectedEmployeeId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'pay_date' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'allowance' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['deduction'] = $validated['deduction'] ?? 0;
        $validated['total'] = $validated['base_salary'] + $validated['allowance'] - $validated['deduction'];
        $validated['created_by'] = auth()->id();

        $payroll = Payroll::create($validated);

        $employee = $payroll->employee;
        $session = CashSession::active()->latest('opened_at')->first();

        Finance::create([
            'cash_session_id' => $session?->id,
            'type' => 'expense',
            'category' => 'Payroll',
            'nominal' => $payroll->total,
            'note' => $validated['note']
                ? $validated['note']
                : 'Pembayaran gaji ' . ($employee?->name ?? 'Karyawan') . ' (' . $payroll->period_start->format('d M Y') . ' - ' . $payroll->period_end->format('d M Y') . ')',
            'recorded_at' => $payroll->pay_date,
            'source' => 'payroll',
            'reference_id' => $payroll->id,
            'reference_type' => 'payroll',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil dicatat dan terhubung ke keuangan.');
    }

    public function show(Payroll $payroll): View
    {
        $payroll->load('employee');

        return view('payrolls.show', compact('payroll'));
    }

    public function slip(Payroll $payroll): View
    {
        $payroll->load('employee');

        $store = [
            'name' => Setting::getValue(Setting::STORE_NAME, config('app.name')),
            'address' => Setting::getValue(Setting::STORE_ADDRESS),
            'phone' => Setting::getValue(Setting::STORE_PHONE),
            'logo' => Setting::getValue(Setting::STORE_LOGO_PATH),
        ];

        return view('payrolls.slip', compact('payroll', 'store'));
    }

    public function edit(Payroll $payroll): View
    {
        $employees = Employee::where('is_active', true)
            ->orWhere('id', $payroll->employee_id)
            ->orderBy('name')
            ->get();

        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'pay_date' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'allowance' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['deduction'] = $validated['deduction'] ?? 0;
        $validated['total'] = $validated['base_salary'] + $validated['allowance'] - $validated['deduction'];

        $payroll->update($validated);

        $payroll->load('employee');
        $employee = $payroll->employee;
        $finance = Finance::where('reference_type', 'payroll')
            ->where('reference_id', $payroll->id)
            ->first();

        $session = CashSession::active()->latest('opened_at')->first();
        $financeData = [
            'cash_session_id' => $finance?->cash_session_id ?? $session?->id,
            'type' => 'expense',
            'category' => 'Payroll',
            'nominal' => $payroll->total,
            'note' => $validated['note']
                ? $validated['note']
                : 'Pembayaran gaji ' . ($employee?->name ?? 'Karyawan') . ' (' . $payroll->period_start->format('d M Y') . ' - ' . $payroll->period_end->format('d M Y') . ')',
            'recorded_at' => $payroll->pay_date,
            'source' => 'payroll',
            'reference_id' => $payroll->id,
            'reference_type' => 'payroll',
        ];

        if ($finance) {
            $finance->update($financeData);
        } else {
            Finance::create($financeData + ['created_by' => auth()->id()]);
        }

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Payroll berhasil diperbarui.');
    }

    public function destroy(Payroll $payroll): RedirectResponse
    {
        Finance::where('reference_type', 'payroll')
            ->where('reference_id', $payroll->id)
            ->delete();

        $payroll->delete();

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil dihapus.');
    }
}
