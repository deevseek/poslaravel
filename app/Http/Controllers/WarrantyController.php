<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    public function index(): View
    {
        Warranty::refreshExpired();

        $warranties = Warranty::with('customer')->latest()->paginate(10);

        return view('warranties.index', [
            'warranties' => $warranties,
        ]);
    }

    public function create(): View
    {
        return view('warranties.create', [
            'customers' => Customer::orderBy('name')->get(),
            'types' => Warranty::types(),
            'statuses' => Warranty::statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', Warranty::types())],
            'reference_id' => ['required', 'integer'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', Warranty::statuses())],
        ]);

        Warranty::create($validated);

        return redirect()->route('warranties.index')->with('success', 'Garansi berhasil dibuat.');
    }

    public function show(Warranty $warranty): View
    {
        Warranty::refreshExpired();

        $warranty->load(['customer', 'claims']);

        return view('warranties.show', [
            'warranty' => $warranty,
            'claimStatuses' => \App\Models\WarrantyClaim::statuses(),
        ]);
    }

    public function edit(Warranty $warranty): View
    {
        return view('warranties.edit', [
            'warranty' => $warranty,
            'customers' => Customer::orderBy('name')->get(),
            'types' => Warranty::types(),
            'statuses' => Warranty::statuses(),
        ]);
    }

    public function update(Request $request, Warranty $warranty): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', Warranty::types())],
            'reference_id' => ['required', 'integer'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', Warranty::statuses())],
        ]);

        $warranty->update($validated);

        return redirect()->route('warranties.show', $warranty)->with('success', 'Garansi diperbarui.');
    }

    public function reminder(): View
    {
        Warranty::refreshExpired();

        $warranties = Warranty::with('customer')
            ->where('status', Warranty::STATUS_ACTIVE)
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('end_date')
            ->get();

        return view('warranties.reminder', [
            'warranties' => $warranties,
        ]);
    }
}
