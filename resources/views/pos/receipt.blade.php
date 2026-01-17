<x-app-layout title="Struk Pembayaran">

<style>
/* =====================================================
   GLOBAL
===================================================== */
.receipt-layout {
    margin: 0 auto;
    background: #fff;
    color: #111827;
    font-family: "Inter", system-ui, -apple-system, sans-serif;
    position: relative;
    box-sizing: border-box;
}

.receipt-standard,
.receipt-thermal-80,
.receipt-thermal-58 {
    display: none;
    position: relative;
    z-index: 1;
}

/* =====================================================
   MODE SWITCH
===================================================== */
.receipt-layout[data-format="standard"] .receipt-standard {
    display: block;
}
.receipt-layout[data-format="thermal80"] .receipt-thermal-80 {
    display: block;
}
.receipt-layout[data-format="thermal58"] .receipt-thermal-58 {
    display: block;
}

/* =====================================================
   PRINT (FINAL & AMAN)
===================================================== */
@media print {
    @page {
        size: A4;
        margin: 10mm; /* KIRI-KANAN SAMA */
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    .receipt-actions {
        display: none !important;
    }

    /* PRINTER BIASA */
    .receipt-layout[data-format="standard"] {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }

    /* THERMAL */
    .receipt-layout[data-format="thermal80"] {
        width: 72mm;
        margin: 0;
    }

    .receipt-layout[data-format="thermal58"] {
        width: 48mm;
        margin: 0;
    }
}

/* =====================================================
   WATERMARK
===================================================== */
.receipt-watermark {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    font-weight: 700;
    color: #000;
    opacity: 0.06;
    transform: rotate(-20deg);
    pointer-events: none;
    white-space: nowrap;
    z-index: 0;
}

/* =====================================================
   STANDARD RECEIPT
===================================================== */
.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.receipt-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.receipt-table th,
.receipt-table td {
    padding: 6px;
    border-bottom: 1px solid #e5e7eb;
}

.receipt-total {
    display: flex;
    justify-content: space-between;
    font-weight: 700;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 2px solid #000;
}

.receipt-note {
    margin-top: 16px;
    text-align: center;
    font-size: 12px;
    color: #6b7280;
}

/* =====================================================
   THERMAL
===================================================== */
.thermal {
    font-family: monospace;
    font-size: 11px;
    line-height: 1.4;
}

.thermal hr {
    border-top: 1px dashed #000;
    margin: 6px 0;
}

.thermal .center {
    text-align: center;
}

.thermal-logo {
    display: block;
    margin: 0 auto 4px;
    max-width: 40mm;
}
</style>

@php
$logo = $store['logo']
    ? (Str::startsWith($store['logo'], ['http','https']) ? $store['logo'] : asset($store['logo']))
    : null;
@endphp

<div class="max-w-5xl mx-auto space-y-4">

    <!-- ACTIONS -->
    <div class="receipt-actions flex flex-wrap gap-2">
        <button data-format="standard" class="border px-3 py-2">Preview Printer</button>
        <button data-format="thermal80" class="border px-3 py-2">Preview Thermal 80mm</button>
        <button data-format="thermal58" class="border px-3 py-2">Preview Thermal 58mm</button>

        <button data-format="standard" data-print class="bg-blue-600 text-white px-4 py-2 rounded">Cetak</button>
        <button data-format="thermal80" data-print class="bg-green-600 text-white px-4 py-2 rounded">Cetak Thermal</button>
    </div>

    <div class="receipt-layout border p-6" data-layout data-format="standard">
        <div class="receipt-watermark">{{ strtoupper($store['name']) }}</div>

        <!-- ================= STANDARD ================= -->
        <div class="receipt-standard">
            <div class="receipt-header">
                <div>
                    <strong>{{ $store['name'] }}</strong><br>
                    {{ $store['address'] }}<br>
                    Telp: {{ $store['phone'] }}
                </div>
                @if($logo)
                    <img src="{{ $logo }}" style="height:70px">
                @endif
            </div>

            <div class="mt-3">
                <div><strong>Invoice:</strong> {{ $transaction->invoice_number }}</div>
                <div><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <table class="receipt-table mt-4">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($transaction->items as $item)
                    <tr>
                        <td>{{ $item->product?->name }}</td>
                        <td align="center">{{ $item->quantity }}</td>
                        <td align="right">{{ number_format($item->price) }}</td>
                        <td align="right">{{ number_format($item->total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="receipt-total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($transaction->total) }}</span>
            </div>

            <div class="receipt-note">Terima kasih telah berbelanja.</div>
        </div>

        <!-- ================= THERMAL 80 ================= -->
        <div class="receipt-thermal-80 thermal">
            <div class="center">
                @if($logo)<img src="{{ $logo }}" class="thermal-logo">@endif
                <strong>{{ $store['name'] }}</strong><br>
                {{ $store['phone'] }}
            </div>

            <hr>

            Invoice: {{ $transaction->invoice_number }}<br>
            {{ $transaction->created_at->format('d/m/Y H:i') }}

            <hr>

            @foreach($transaction->items as $item)
                <div><strong>{{ $item->product?->name }}</strong></div>
                <div style="display:flex;justify-content:space-between">
                    <span>{{ $item->quantity }} x {{ number_format($item->price) }}</span>
                    <span>{{ number_format($item->total) }}</span>
                </div>
            @endforeach

            <hr>

            <div style="display:flex;justify-content:space-between;font-weight:bold">
                <span>TOTAL</span>
                <span>{{ number_format($transaction->total) }}</span>
            </div>
        </div>

        <!-- ================= THERMAL 58 ================= -->
        <div class="receipt-thermal-58 thermal">
            <div class="center">
                <strong>{{ $store['name'] }}</strong><br>
                {{ $store['phone'] }}
            </div>

            <hr>

            @foreach($transaction->items as $item)
                <div><strong>{{ Str::limit($item->product?->name, 22) }}</strong></div>
                <div style="display:flex;justify-content:space-between">
                    <span>{{ $item->quantity }} x {{ number_format($item->price) }}</span>
                    <span>{{ number_format($item->total) }}</span>
                </div>
            @endforeach

            <hr>

            <div style="display:flex;justify-content:space-between;font-weight:bold">
                <span>TOTAL</span>
                <span>{{ number_format($transaction->total) }}</span>
            </div>
        </div>

    </div>
</div>

<script>
const layout = document.querySelector('[data-layout]');
document.querySelectorAll('[data-format]').forEach(btn => {
    btn.onclick = () => {
        layout.dataset.format = btn.dataset.format;
        if (btn.hasAttribute('data-print')) window.print();
    };
});
</script>

</x-app-layout>
