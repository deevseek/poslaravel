<x-app-layout title="Keuangan">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Keuangan</h1>
                <p class="text-gray-600">Catat pemasukan & pengeluaran serta kelola kas harian.</p>
            </div>
            <form method="GET" action="{{ route('finances.index') }}" class="flex items-center gap-3">
                <label for="month" class="text-sm font-medium text-gray-700">Periode</label>
                <input type="month" id="month" name="month" value="{{ $month }}"
                    class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Filter</button>
                <a href="{{ route('finances.export', ['month' => $month]) }}"
                    class="rounded-lg border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-50">Ekspor CSV</a>
            </form>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Kas Harian</h2>
                @if ($activeSession)
                    <p class="mt-1 text-sm text-gray-600">Kas dibuka pada {{ $activeSession->opened_at->format('d M Y H:i') }}.</p>

                    @php
                        $sessionIncome = $activeSession->finances()->where('type', 'income')->sum('nominal');
                        $sessionExpense = $activeSession->finances()->where('type', 'expense')->sum('nominal');
                        $currentBalance = $activeSession->opening_balance + $sessionIncome - $sessionExpense;
                    @endphp

                    <dl class="mt-4 space-y-2 text-sm text-gray-700">
                        <div class="flex justify-between"><dt>Saldo awal</dt><dd class="font-semibold">Rp {{ number_format($activeSession->opening_balance, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between text-green-700"><dt>Pemasukan</dt><dd class="font-semibold">+ Rp {{ number_format($sessionIncome, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between text-red-700"><dt>Pengeluaran</dt><dd class="font-semibold">- Rp {{ number_format($sessionExpense, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt>Perkiraan saldo</dt><dd class="font-semibold">Rp {{ number_format($currentBalance, 0, ',', '.') }}</dd></div>
                    </dl>

                    <form action="{{ route('finances.cash.close') }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700" for="note">Catatan penutupan (opsional)</label>
                        <textarea id="note" name="note" rows="2"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tutup Kas</button>
                    </form>
                @else
                    <p class="mt-1 text-sm text-gray-600">Belum ada kas yang dibuka hari ini.</p>
                    <form action="{{ route('finances.cash.open') }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="opening_balance">Saldo awal</label>
                            <input type="number" min="0" step="0.01" id="opening_balance" name="opening_balance" required
                                value="{{ old('opening_balance', 0) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            @error('opening_balance')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="note">Catatan (opsional)</label>
                            <textarea id="note" name="note" rows="2"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Buka Kas</button>
                    </form>
                @endif

                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-800">Riwayat Kas Terakhir</h3>
                    <ul class="mt-2 space-y-2 text-sm text-gray-700">
                        @forelse ($recentSessions as $session)
                            <li class="flex justify-between rounded-lg border border-gray-100 px-3 py-2">
                                <div>
                                    <p class="font-semibold">{{ $session->opened_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">Saldo awal: Rp {{ number_format($session->opening_balance, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    @if ($session->closed_at)
                                        <p class="text-xs text-gray-500">Ditutup {{ $session->closed_at->format('H:i') }}</p>
                                        <p class="font-semibold">Rp {{ number_format($session->closing_balance, 0, ',', '.') }}</p>
                                    @else
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Aktif</span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500">Belum ada riwayat kas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900">Catat Pemasukan</h2>
                        <form action="{{ route('finances.income.store') }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="income_recorded_at">Tanggal</label>
                                <input type="date" id="income_recorded_at" name="recorded_at" value="{{ old('recorded_at', now()->toDateString()) }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('recorded_at')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="income_category">Kategori</label>
                                <input type="text" id="income_category" name="category" value="{{ old('category') }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('category')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="income_nominal">Nominal</label>
                                <input type="number" id="income_nominal" name="nominal" min="0" step="0.01" value="{{ old('nominal') }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('nominal')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="income_note">Catatan</label>
                                <textarea id="income_note" name="note" rows="2"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('note') }}</textarea>
                                @error('note')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Simpan Pemasukan</button>
                        </form>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900">Catat Pengeluaran</h2>
                        <form action="{{ route('finances.expense.store') }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="expense_recorded_at">Tanggal</label>
                                <input type="date" id="expense_recorded_at" name="recorded_at" value="{{ old('recorded_at', now()->toDateString()) }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('recorded_at')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="expense_category">Kategori</label>
                                <input type="text" id="expense_category" name="category" value="{{ old('category') }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('category')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="expense_nominal">Nominal</label>
                                <input type="number" id="expense_nominal" name="nominal" min="0" step="0.01" value="{{ old('nominal') }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('nominal')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="expense_note">Catatan</label>
                                <textarea id="expense_note" name="note" rows="2"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('note') }}</textarea>
                                @error('note')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Simpan Pengeluaran</button>
                        </form>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Laporan Bulanan</h2>
                            <p class="text-sm text-gray-600">Periode {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</p>
                        </div>
                        <div class="flex gap-4 text-sm">
                            <span class="rounded-full bg-green-100 px-3 py-1 font-semibold text-green-700">Pemasukan: Rp {{ number_format($incomeTotal, 0, ',', '.') }}</span>
                            <span class="rounded-full bg-red-100 px-3 py-1 font-semibold text-red-700">Pengeluaran: Rp {{ number_format($expenseTotal, 0, ',', '.') }}</span>
                            <span class="rounded-full bg-blue-100 px-3 py-1 font-semibold text-blue-700">Saldo: Rp {{ number_format($incomeTotal - $expenseTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">Omset POS</p>
                            <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($posIncome, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">Omset Service</p>
                            <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($serviceIncome, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">HPP POS</p>
                            <p class="text-xl font-semibold text-red-700">Rp {{ number_format($posHpp, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">HPP Service</p>
                            <p class="text-xl font-semibold text-red-700">Rp {{ number_format($serviceHpp, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">Biaya Operasional</p>
                            <p class="text-xl font-semibold text-red-700">Rp {{ number_format($total_expense, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">Laba Kotor</p>
                            <p class="text-xl font-semibold text-green-700">Rp {{ number_format($gross_profit, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">Laba Bersih</p>
                            <p class="text-xl font-semibold text-blue-700">Rp {{ number_format($net_profit, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 overflow-hidden rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nominal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($finances as $finance)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $finance->recorded_at->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $finance->type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $finance->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $finance->category }}</span>
                                                @if ($finance->source === 'pos')
                                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold uppercase text-blue-700">POS</span>
                                                @elseif ($finance->source === 'service')
                                                    <span class="rounded-full bg-purple-100 px-2 py-0.5 text-[11px] font-semibold uppercase text-purple-700">SERVICE</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">Rp {{ number_format($finance->nominal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $finance->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada transaksi untuk periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $finances->appends(['month' => $month])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
