<?php

namespace App\Http\Controllers;

use App\Tenancy\Models\SubscriptionPlan;
use App\Tenancy\Models\Subscription;
use App\Tenancy\Models\Tenant;
use App\Tenancy\Services\TenantProvisioningService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::with(['plan', 'latestSubscription'])->latest()->paginate(10);

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
        $tenant->load('latestSubscription');

        return view('tenants.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'plan_id' => ['nullable', 'exists:subscription_plans,id'],
            'subscription_end_date' => ['nullable', 'date'],
            'auto_renew' => ['nullable', 'boolean'],
        ]);

        $tenant->update($validated);
        $this->syncSubscription($tenant, $validated);

        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil diperbarui.');
    }

    public function syncMigrations(): RedirectResponse
    {
        try {
            Artisan::call('tenant:migrate');
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('tenants.index')
                ->withErrors(['general' => 'Gagal menyinkronkan tabel tenant. Silakan coba lagi.']);
        }

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Sinkronisasi tabel tenant berhasil dijalankan.');
    }

    public function syncRoles(): RedirectResponse
    {
        try {
            Artisan::call('tenant:seed');
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('tenants.index')
                ->withErrors(['general' => 'Gagal menyinkronkan role tenant. Silakan coba lagi.']);
        }

        return redirect()
            ->route('tenants.index')
            ->with('success', 'Sinkronisasi role tenant berhasil dijalankan.');
    }

    protected function syncSubscription(Tenant $tenant, array $validated): void
    {
        if (! array_key_exists('plan_id', $validated) || ! $validated['plan_id']) {
            return;
        }

        $endDate = $validated['subscription_end_date'] ?? null;
        $autoRenew = (bool) ($validated['auto_renew'] ?? false);
        $status = 'active';

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            if (Carbon::now()->gt($endDate)) {
                $status = 'expired';
            }
        }

        $subscription = $tenant->latestSubscription;

        if ($subscription) {
            $subscription->fill([
                'plan_id' => $validated['plan_id'],
                'end_date' => $endDate,
                'status' => $status,
                'auto_renew' => $autoRenew,
            ])->save();

            return;
        }

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $validated['plan_id'],
            'start_date' => now(),
            'end_date' => $endDate,
            'status' => $status,
            'auto_renew' => $autoRenew,
        ]);
    }
}
