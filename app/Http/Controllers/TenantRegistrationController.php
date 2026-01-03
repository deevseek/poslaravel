<?php

namespace App\Http\Controllers;

use App\Mail\TenantRegistrationApproved;
use App\Tenancy\Models\SubscriptionPlan;
use App\Tenancy\Models\Tenant;
use App\Tenancy\Models\TenantRegistration;
use App\Tenancy\Services\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class TenantRegistrationController extends Controller
{
    public function landing(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $plans = SubscriptionPlan::orderBy('price')->get();

        return view('landing', compact('plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50', 'alpha_dash'],
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'payment_method' => ['required', Rule::in(['transfer', 'e-wallet', 'cash'])],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'agreement' => ['required', 'accepted'],
        ]);

        $subdomain = Str::slug($validated['subdomain']);

        if (Tenant::where('subdomain', $subdomain)->exists() || TenantRegistration::where('subdomain', $subdomain)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['subdomain' => 'Subdomain sudah digunakan. Silakan pilih nama lain.']);
        }

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')
                ->store('tenant-registrations', 'public');
        }

        TenantRegistration::create([
            'name' => $validated['name'],
            'subdomain' => $subdomain,
            'admin_name' => $validated['admin_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_amount' => $plan->price,
            'payment_proof_path' => $paymentProofPath,
            'password_encrypted' => Crypt::encryptString($validated['password']),
        ]);

        return back()->with('success', 'Pendaftaran Anda sudah kami terima. Tim kami akan memverifikasi pembayaran dan mengaktifkan tenant Anda.');
    }

    public function index(): View
    {
        $registrations = TenantRegistration::with('plan')
            ->latest()
            ->paginate(10);

        return view('tenants.registrations.index', compact('registrations'));
    }

    public function approve(TenantRegistration $tenantRegistration, TenantProvisioningService $service): RedirectResponse
    {
        if ($tenantRegistration->status !== 'pending') {
            return back()->withErrors(['general' => 'Pendaftaran ini sudah diproses.']);
        }

        if (Tenant::where('subdomain', $tenantRegistration->subdomain)->exists()) {
            return back()->withErrors(['general' => 'Subdomain sudah digunakan oleh tenant lain.']);
        }

        $payload = [
            'name' => $tenantRegistration->name,
            'subdomain' => $tenantRegistration->subdomain,
            'plan_id' => $tenantRegistration->plan_id,
            'admin_name' => $tenantRegistration->admin_name,
            'email' => $tenantRegistration->email,
        ];
        try {
            $payload['password'] = Crypt::decryptString($tenantRegistration->password_encrypted);
        } catch (DecryptException) {
            $info = Hash::info($tenantRegistration->password_encrypted);

            if (($info['algo'] ?? 0) !== 0) {
                $payload['password_hash'] = $tenantRegistration->password_encrypted;
            } else {
                $payload['password'] = $tenantRegistration->password_encrypted;
            }
        }

        try {
            $service->provision($payload);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['general' => 'Gagal mengaktifkan tenant. Silakan coba lagi.']);
        }

        $tenantRegistration->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        Mail::to($tenantRegistration->email)->send(new TenantRegistrationApproved($tenantRegistration));

        return back()->with('success', 'Tenant berhasil diaktifkan dan notifikasi email telah dikirim.');
    }

    public function reject(Request $request, TenantRegistration $tenantRegistration): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($tenantRegistration->status !== 'pending') {
            return back()->withErrors(['general' => 'Pendaftaran ini sudah diproses.']);
        }

        if ($tenantRegistration->payment_proof_path) {
            Storage::disk('public')->delete($tenantRegistration->payment_proof_path);
        }

        $tenantRegistration->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        return back()->with('success', 'Pendaftaran ditolak.');
    }
}
