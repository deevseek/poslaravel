<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(Request $request): View
    {
        $keys = [
            Setting::STORE_NAME,
            Setting::STORE_ADDRESS,
            Setting::STORE_PHONE,
            Setting::STORE_HOURS,
            Setting::TRANSACTION_PREFIX,
            Setting::TRANSACTION_PADDING,
            Setting::STORE_LOGO_PATH,
            Setting::WHATSAPP_ENABLED,
            Setting::WHATSAPP_GATEWAY_URL,
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
        $tab = $request->get('tab', 'general');
        $tab = $tab === 'whatsapp' ? 'whatsapp' : 'general';

        return view('settings.index', compact('settings', 'tab'));
    }

    public function update(Request $request): RedirectResponse
    {
        $current = Setting::whereIn('key', [
            Setting::STORE_NAME,
            Setting::STORE_ADDRESS,
            Setting::STORE_PHONE,
            Setting::STORE_HOURS,
            Setting::TRANSACTION_PREFIX,
            Setting::TRANSACTION_PADDING,
            Setting::STORE_LOGO_PATH,
            Setting::WHATSAPP_ENABLED,
            Setting::WHATSAPP_GATEWAY_URL,
        ])->pluck('value', 'key');

        $payload = [
            Setting::STORE_NAME => $request->input(Setting::STORE_NAME, $current[Setting::STORE_NAME] ?? ''),
            Setting::STORE_ADDRESS => $request->input(Setting::STORE_ADDRESS, $current[Setting::STORE_ADDRESS] ?? ''),
            Setting::STORE_PHONE => $request->input(Setting::STORE_PHONE, $current[Setting::STORE_PHONE] ?? ''),
            Setting::STORE_HOURS => $request->input(Setting::STORE_HOURS, $current[Setting::STORE_HOURS] ?? ''),
            Setting::TRANSACTION_PREFIX => $request->input(Setting::TRANSACTION_PREFIX, $current[Setting::TRANSACTION_PREFIX] ?? ''),
            Setting::TRANSACTION_PADDING => $request->input(Setting::TRANSACTION_PADDING, $current[Setting::TRANSACTION_PADDING] ?? 4),
            Setting::STORE_LOGO_PATH => $request->input(Setting::STORE_LOGO_PATH, $current[Setting::STORE_LOGO_PATH] ?? ''),
            Setting::WHATSAPP_ENABLED => $request->boolean(Setting::WHATSAPP_ENABLED, filter_var($current[Setting::WHATSAPP_ENABLED] ?? false, FILTER_VALIDATE_BOOLEAN)),
            Setting::WHATSAPP_GATEWAY_URL => $request->input(Setting::WHATSAPP_GATEWAY_URL, $current[Setting::WHATSAPP_GATEWAY_URL] ?? null),
        ];

        $validated = validator($payload, [
            Setting::STORE_NAME => ['required', 'string', 'max:255'],
            Setting::STORE_ADDRESS => ['required', 'string'],
            Setting::STORE_PHONE => ['required', 'string', 'max:255'],
            Setting::STORE_HOURS => ['required', 'string', 'max:255'],
            Setting::TRANSACTION_PREFIX => ['required', 'string', 'max:10'],
            Setting::TRANSACTION_PADDING => ['required', 'integer', 'min:1', 'max:10'],
            Setting::STORE_LOGO_PATH => ['nullable', 'string', 'max:255'],
            Setting::WHATSAPP_ENABLED => ['boolean'],
            Setting::WHATSAPP_GATEWAY_URL => ['nullable', 'url'],
        ])->validate();

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
