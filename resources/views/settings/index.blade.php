<x-app-layout title="Pengaturan">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Pengaturan</h1>
            <p class="text-gray-600">Sesuaikan identitas toko dan integrasi WhatsApp.</p>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex gap-4 border-b border-gray-200">
            <a href="{{ route('settings.index', ['tab' => 'general']) }}"
                class="px-4 py-2 text-sm font-semibold {{ $tab === 'whatsapp' ? 'text-gray-600' : 'border-b-2 border-blue-600 text-blue-700' }}">Umum</a>
            <a href="{{ route('settings.index', ['tab' => 'whatsapp']) }}"
                class="px-4 py-2 text-sm font-semibold {{ $tab === 'whatsapp' ? 'border-b-2 border-blue-600 text-blue-700' : 'text-gray-600' }}">WhatsApp</a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
                @csrf

                @if ($tab === 'whatsapp')
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Aktifkan WhatsApp</p>
                            <p class="text-sm text-gray-600">Gunakan Baileys gateway untuk mengirim pesan otomatis.</p>
                        </div>
                        <label class="inline-flex cursor-pointer items-center">
                            <input type="hidden" name="{{ App\Models\Setting::WHATSAPP_ENABLED }}" value="0">
                            <input type="checkbox" name="{{ App\Models\Setting::WHATSAPP_ENABLED }}" value="1"
                                class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ ($settings[App\Models\Setting::WHATSAPP_ENABLED] ?? false) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::WHATSAPP_GATEWAY_URL }}">Gateway URL</label>
                        <input type="url" id="{{ App\Models\Setting::WHATSAPP_GATEWAY_URL }}"
                            name="{{ App\Models\Setting::WHATSAPP_GATEWAY_URL }}"
                            value="{{ old(App\Models\Setting::WHATSAPP_GATEWAY_URL, $settings[App\Models\Setting::WHATSAPP_GATEWAY_URL] ?? '') }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://gateway-baileys.local/send">
                        <p class="mt-1 text-sm text-gray-500">Endpoint HTTP POST yang menerima parameter <code>phone</code> dan <code>message</code>.</p>
                        @error(App\Models\Setting::WHATSAPP_GATEWAY_URL)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Pengaturan WhatsApp</button>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::STORE_NAME }}">Nama Toko</label>
                        <input type="text" id="{{ App\Models\Setting::STORE_NAME }}"
                            name="{{ App\Models\Setting::STORE_NAME }}"
                            value="{{ old(App\Models\Setting::STORE_NAME, $settings[App\Models\Setting::STORE_NAME] ?? '') }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        @error(App\Models\Setting::STORE_NAME)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::STORE_ADDRESS }}">Alamat Toko</label>
                        <textarea id="{{ App\Models\Setting::STORE_ADDRESS }}" name="{{ App\Models\Setting::STORE_ADDRESS }}" rows="2"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old(App\Models\Setting::STORE_ADDRESS, $settings[App\Models\Setting::STORE_ADDRESS] ?? '') }}</textarea>
                        @error(App\Models\Setting::STORE_ADDRESS)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::STORE_PHONE }}">Nomor HP</label>
                            <input type="text" id="{{ App\Models\Setting::STORE_PHONE }}"
                                name="{{ App\Models\Setting::STORE_PHONE }}"
                                value="{{ old(App\Models\Setting::STORE_PHONE, $settings[App\Models\Setting::STORE_PHONE] ?? '') }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error(App\Models\Setting::STORE_PHONE)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::STORE_HOURS }}">Jam Operasional</label>
                            <input type="text" id="{{ App\Models\Setting::STORE_HOURS }}"
                                name="{{ App\Models\Setting::STORE_HOURS }}"
                                value="{{ old(App\Models\Setting::STORE_HOURS, $settings[App\Models\Setting::STORE_HOURS] ?? '') }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error(App\Models\Setting::STORE_HOURS)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::TRANSACTION_PREFIX }}">Prefix Nomor Transaksi</label>
                            <input type="text" id="{{ App\Models\Setting::TRANSACTION_PREFIX }}"
                                name="{{ App\Models\Setting::TRANSACTION_PREFIX }}"
                                value="{{ old(App\Models\Setting::TRANSACTION_PREFIX, $settings[App\Models\Setting::TRANSACTION_PREFIX] ?? 'INV') }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error(App\Models\Setting::TRANSACTION_PREFIX)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::TRANSACTION_PADDING }}">Padding Nomor</label>
                            <input type="number" min="1" max="10" id="{{ App\Models\Setting::TRANSACTION_PADDING }}"
                                name="{{ App\Models\Setting::TRANSACTION_PADDING }}"
                                value="{{ old(App\Models\Setting::TRANSACTION_PADDING, $settings[App\Models\Setting::TRANSACTION_PADDING] ?? 4) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error(App\Models\Setting::TRANSACTION_PADDING)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::STORE_LOGO_PATH }}">Logo Toko</label>
                        <input type="file" id="{{ App\Models\Setting::STORE_LOGO_PATH }}"
                            name="{{ App\Models\Setting::STORE_LOGO_PATH }}"
                            accept="image/*"
                            class="mt-2 w-full rounded-lg border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @if (!empty($settings[App\Models\Setting::STORE_LOGO_PATH]))
                            <div class="mt-3 flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                <img src="{{ asset($settings[App\Models\Setting::STORE_LOGO_PATH]) }}" alt="Logo toko" class="h-12 w-12 rounded object-cover">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Logo saat ini</p>
                                    <p class="text-xs text-gray-500">{{ $settings[App\Models\Setting::STORE_LOGO_PATH] }}</p>
                                </div>
                            </div>
                        @endif
                        @error(App\Models\Setting::STORE_LOGO_PATH)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Pengaturan Umum</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>
