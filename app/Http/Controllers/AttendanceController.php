<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $attendances = Attendance::with('employee')
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_time')
            ->paginate(10);

        return view('attendances.index', compact('attendances'));
    }

    public function create(): View
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('attendances', 'employee_id')->where(fn ($query) => $query
                    ->where('employee_id', $request->employee_id)
                    ->where('attendance_date', $request->attendance_date)),
            ],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i', 'after_or_equal:check_in_time'],
            'note' => ['nullable', 'string', 'max:500'],
            'retina_scan_code' => ['required', 'string', 'max:255'],
        ], [
            'employee_id.unique' => 'Absensi untuk karyawan dan tanggal tersebut sudah ada.',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        if (! $employee->retina_signature || ! $employee->retina_registered_at) {
            return back()
                ->withErrors(['employee_id' => 'Retina karyawan belum terdaftar. Silakan daftar terlebih dahulu di profil karyawan.'])
                ->withInput();
        }

        if (! Hash::check($validated['retina_scan_code'], $employee->retina_signature)) {
            return back()
                ->withErrors(['retina_scan_code' => 'Scan retina tidak cocok dengan data terdaftar.'])
                ->withInput();
        }

        $checkIn = Carbon::createFromFormat('H:i', $validated['check_in_time']);
        $status = $checkIn->greaterThan(Carbon::createFromFormat('H:i', '09:00')) ? 'Terlambat' : 'Hadir';

        Attendance::create([
            'employee_id' => $validated['employee_id'],
            'attendance_date' => $validated['attendance_date'],
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'] ?? null,
            'method' => 'retina_scan',
            'status' => $status,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil dicatat dengan metode scan retina via webcam.');
    }

    public function show(Attendance $attendance): View
    {
        $attendance->load('employee');

        return view('attendances.show', compact('attendance'));
    }
}
