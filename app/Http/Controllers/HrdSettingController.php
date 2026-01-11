<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrdSettingController extends Controller
{
    public function index(): View
    {
        $keys = [
            Setting::HRD_WORK_START,
            Setting::HRD_WORK_END,
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

        return view('hrd.settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            Setting::HRD_WORK_START => ['required', 'date_format:H:i'],
            Setting::HRD_WORK_END => ['required', 'date_format:H:i', 'after:'.Setting::HRD_WORK_START],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()
            ->route('hrd-settings.index')
            ->with('success', 'Pengaturan HRD berhasil diperbarui.');
    }
}
