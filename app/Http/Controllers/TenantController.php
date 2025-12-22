<?php

namespace App\Http\Controllers;

use App\Tenancy\Models\SubscriptionPlan;
use App\Tenancy\Models\Tenant;
use App\Tenancy\Services\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::with('plan')->latest()->paginate(10);

        return view('tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        $plans = SubscriptionPlan::orderBy('price')->get();

        return view('tenants.create', compact('plans'));
    }

    public function store(Request $request, TenantProvisioningService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:tenants,subdomain'],
            'plan_id' => ['nullable', 'exists:subscription_plans,id'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $service->provision($validated);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Gagal membuat tenant. Silakan coba lagi atau hubungi administrator.']);
        }

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil dibuat.');
    }

    public function edit(Tenant $tenant): View
    {
        $plans = SubscriptionPlan::orderBy('price')->get();

        return view('tenants.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'plan_id' => ['nullable', 'exists:subscription_plans,id'],
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil diperbarui.');
    }
}
