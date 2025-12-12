<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\WaLog;
use App\Models\WaTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaBroadcastController extends Controller
{
    public function __construct(private WhatsAppService $whatsAppService)
    {
    }

    public function index(): View
    {
        $templates = WaTemplate::where('is_active', true)->orderBy('title')->get();

        return view('wa.broadcast', compact('templates'));
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => ['nullable', 'exists:wa_templates,id'],
            'message' => ['nullable', 'string', 'required_without:template_id'],
        ]);

        $message = $validated['message'] ?? null;

        if ($validated['template_id'] ?? false) {
            $template = WaTemplate::find($validated['template_id']);
            if ($template && ! $template->is_active) {
                return back()->with('error', 'Template tidak aktif.');
            }
            $message = $message ?: $template?->message;
        }

        if (! $message) {
            return back()->with('error', 'Pesan tidak boleh kosong.');
        }

        $customers = Customer::whereNotNull('phone')->get();
        $storeName = Setting::getValue(Setting::STORE_NAME, config('app.name'));
        $sentCount = 0;

        foreach ($customers as $customer) {
            $personalized = str_replace(
                ['{{nama}}', '{{nama_toko}}'],
                [$customer->name, $storeName],
                $message
            );

            if ($this->whatsAppService->sendMessage($customer->phone, $personalized, 'promo')) {
                $sentCount++;
            }
        }

        return back()->with('success', 'Broadcast dikirim ke ' . $sentCount . ' pelanggan.');
    }

    public function logs(): View
    {
        $logs = WaLog::latest()->paginate(20);

        return view('wa.logs', compact('logs'));
    }
}
