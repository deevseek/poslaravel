<x-app-layout title="Struk Pembayaran">
<style>
/* ================== GLOBAL ================== */
.receipt-layout {
    margin: 0 auto;
    background: white;
    color: #111827;
    font-family: "Inter", ui-sans-serif, system-ui, -apple-system, sans-serif;
    position: relative;
    overflow: hidden;
}

.receipt-standard,
.receipt-thermal-80,
.receipt-thermal-58 {
    display: none;
    position: relative;
    z-index: 1;
}

/* ================== MODE SWITCH ================== */
.receipt-layout[data-format="standard"] .receipt-standard {
    display: block;
}

.receipt-layout[data-format="thermal80"] .receipt-thermal-80 {
    display: block;
}

.receipt-layout[data-format="thermal58"] .receipt-thermal-58 {
    display: block;
}

/* ================== PRINT ================== */
@media print {
    @page {
        size: auto;
        margin: 6mm;
    }

    body {
        background: white !important;
    }

    .receipt-actions {
        display: none !important;
    }

    .receipt-layout {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }

    /* PRINTER BIASA */
    .receipt-layout[data-format="standard"] {
        width: 100%;
        max-width: 210mm;
        font-size: 12px;
    }

    /* THERMAL 80MM */
    .receipt-layout[data-format="thermal80"] {
        width: 72mm;
        height: 297mm;
        font-size: 11px;
    }

    /* THERMAL 58MM */
    .receipt-layout[data-format="thermal58"] {
        width: 48mm;
        height: 297mm;
        font-size: 10px;
    }
}

/* ================== THERMAL ================== */
.thermal {
    font-family: monospace;
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
    max-width: 60mm;
}

.receipt-layout[data-format="thermal80"] .thermal-logo {
    max-width: 40mm;
    max-height: 18mm;
}

.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
}

.receipt-store {
    line-height: 1.5;
}

.receipt-meta {
    font-size: 12px;
    color: #6b7280;
}

.receipt-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.receipt-table th {
    text-align: left;
    font-weight: 600;
    color: #374151;
    padding: 8px 6px;
    border-bottom: 1px solid #e5e7eb;
}

.receipt-table td {
    padding: 8px 6px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.receipt-total {
    display: flex;
    justify-content: space-between;
    font-weight: 700;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e5e7eb;
}

.receipt-note {
    font-size: 11px;
    color: #6b7280;
    text-align: center;
    margin-top: 14px;
}

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
    z-index: 0;
    pointer-events: none;
    text-align: center;
    white-space: nowrap;
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
        <button data-format="standard" class="btn">Preview Printer Biasa</button>
        <button data-format="thermal80" class="btn">Preview Thermal 80mm</button>
        <button data-format="thermal58" class="btn">Preview Thermal 58mm</button>

        <button data-format="standard" data-print="true" class="bg-blue-600 text-white px-4 py-2 rounded">Cetak</button>
        <button data-format="thermal80" data-print="true" class="bg-green-600 text-white px-4 py-2 rounded">Cetak Thermal</button>
        <button data-format="thermal58" data-print="true" class="bg-emerald-600 text-white px-4 py-2 rounded">Cetak Thermal 58mm</button>

        <a href="{{ route('pos.receiver.print', $transaction) }}"
           class="bg-indigo-50 border px-4 py-2 rounded text-indigo-700">
            Cetak Penerima
        </a>
    </div>

    <div class="receipt-layout border p-6" data-layout data-format="standard">
        <div class="receipt-watermark">{{ strtoupper($store['name']) }}</div>

        <!-- ================= PRINTER BIASA ================= -->
        <div class="receipt-standard">
            <div class="receipt-header">
                <div class="receipt-store">
                    <div class="text-lg font-semibold">{{ $store['name'] }}</div>
                    <div>{{ $store['address'] }}</div>
                    <div>Telp: {{ $store['phone'] }}</div>
                </div>
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo {{ $store['name'] }}" style="height:70px">
                @endif
            </div>

            <div class="receipt-meta mt-3">
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
                <span>Total</span>
                <span>Rp {{ number_format($transaction->total) }}</span>
            </div>

            <div class="receipt-note">Terima kasih telah berbelanja.</div>
        </div>

        <!-- ================= THERMAL 80MM ================= -->
        <div class="receipt-thermal-80 thermal">
            <div class="center">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo {{ $store['name'] }}" class="thermal-logo">
                @endif
                <strong>{{ $store['name'] }}</strong><br>
                {{ $store['address'] }}<br>
                {{ $store['phone'] }}
            </div>

            <hr>

            <div>Invoice: {{ $transaction->invoice_number }}</div>
            <div>{{ $transaction->created_at->format('d/m/Y H:i') }}</div>

            <hr>

            @foreach($transaction->items as $item)
                <div><strong>{{ $item->product?->name }}</strong></div>
                <div class="flex justify-between">
                    <span>{{ $item->quantity }} x {{ number_format($item->price) }}</span>
                    <span>{{ number_format($item->total) }}</span>
                </div>
            @endforeach

            <hr>
            <div class="flex justify-between">
                <strong>TOTAL</strong>
                <strong>{{ number_format($transaction->total) }}</strong>
            </div>
            <div class="center text-xs mt-2">Terima kasih.</div>
        </div>

        <!-- ================= THERMAL 58MM ================= -->
        <div class="receipt-thermal-58 thermal">
            <div class="center">
                <strong>{{ $store['name'] }}</strong><br>
                {{ $store['address'] }}<br>
                {{ $store['phone'] }}
            </div>

            <hr>

            <div>Invoice: {{ $transaction->invoice_number }}</div>
            <div>{{ $transaction->created_at->format('d/m/Y H:i') }}</div>

            @foreach($transaction->items as $item)
                <div><strong>{{ Str::limit($item->product?->name, 22) }}</strong></div>
                <div class="flex justify-between">
                    <span>{{ $item->quantity }} x {{ number_format($item->price) }}</span>
                    <span>{{ number_format($item->total) }}</span>
                </div>
            @endforeach

            <hr>
            <div class="flex justify-between">
                <strong>TOTAL</strong>
                <strong>{{ number_format($transaction->total) }}</strong>
            </div>
            <div class="center text-xs mt-2">Terima kasih.</div>
        </div>

    </div>
</div>

<style id="print-page-style"></style>
<script>
const layout = document.querySelector('[data-layout]');
const pageStyle = document.getElementById('print-page-style');
const pageSizes = {
    standard: { size: 'A4', margin: '10mm' },
    thermal80: { size: '72mm 297mm', margin: '4mm' },
    thermal58: { size: '48mm 297mm', margin: '4mm' },
};

const applyPageStyle = (format) => {
    const config = pageSizes[format] ?? pageSizes.standard;
    pageStyle.textContent = `@media print { @page { size: ${config.size}; margin: ${config.margin}; } }`;
};

document.querySelectorAll('.receipt-actions [data-format]').forEach(btn => {
    btn.onclick = () => {
        const format = btn.dataset.format;
        layout.dataset.format = format;
        applyPageStyle(format);
        if (btn.hasAttribute('data-print')) {
            window.print();
        }
    };
});

applyPageStyle(layout.dataset.format);
</script>
</x-app-layout>
