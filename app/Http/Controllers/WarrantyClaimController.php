<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\WarrantyClaim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WarrantyClaimController extends Controller
{
    public function store(Request $request, Warranty $warranty): RedirectResponse
    {
        $validated = $request->validate([
            'claim_date' => ['required', 'date'],
            'technician_notes' => ['required', 'string'],
        ]);

        $warranty->claims()->create([
            'claim_date' => $validated['claim_date'],
            'technician_notes' => $validated['technician_notes'],
            'status' => WarrantyClaim::STATUS_PENDING,
        ]);

        return back()->with('success', 'Klaim garansi ditambahkan.');
    }

    public function update(Request $request, WarrantyClaim $warrantyClaim): RedirectResponse
    {
        $validated = $request->validate([
            'claim_date' => ['required', 'date'],
            'technician_notes' => ['required', 'string'],
            'status' => ['required', 'in:' . implode(',', WarrantyClaim::statuses())],
            'resolution' => ['nullable', 'string'],
        ]);

        $warrantyClaim->update($validated);

        $warranty = $warrantyClaim->warranty;

        if ($validated['status'] === WarrantyClaim::STATUS_APPROVED && $warranty->status !== Warranty::STATUS_EXPIRED) {
            $warranty->update(['status' => Warranty::STATUS_CLAIMED]);
        } elseif ($validated['status'] !== WarrantyClaim::STATUS_APPROVED && $warranty->status === Warranty::STATUS_CLAIMED) {
            $warranty->update(['status' => Warranty::STATUS_ACTIVE]);
        }

        return back()->with('success', 'Klaim garansi diperbarui.');
    }
}
