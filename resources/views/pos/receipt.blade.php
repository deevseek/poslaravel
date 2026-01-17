<x-app-layout title="Struk Pembayaran">
<style>
/* ================== GLOBAL ================== */
.receipt-layout {
    margin: 0 auto;
    background: white;
}

.receipt-standard,
.receipt-thermal-80,
.receipt-thermal-58 {
    display: none;
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
        width: 80mm;
        font-size: 11px;
    }

    /* THERMAL 58MM */
    .receipt-layout[data-format="thermal58"] {
        width: 58mm;
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

        <button data-format="standard" data-print class="bg-blue-600 text-white px-4 py-2 rounded">Cetak</button>
        <button data-format="thermal80" data-print class="bg-green-600 text-white px-4 py-2 rounded">Cetak Thermal</button>

        <a href="{{ route('pos.receiver.print', $transaction) }}"
           class="bg-indigo-50 border px-4 py-2 rounded text-indigo-700">
            Cetak Penerima
        </a>
    </div>

    <div class="receipt-layout border p-6" data-layout data-format="standard">

        <!-- ================= PRINTER BIASA ================= -->
        <div class="receipt-standard">
            <div class="flex justify-between items-start">
                <div>
                    <strong>{{ $store['name'] }}</strong><br>
                    {{ $store['address'] }}<br>
                    Telp: {{ $store['phone'] }}
                </div>
                @if($logo)
                    <img src="{{ $logo }}" style="height:70px">
                @endif
            </div>

            <hr>

            <p><strong>Invoice:</strong> {{ $transaction->invoice_number }}</p>
            <p><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</p>

            <table width="100%" border="1" cellspacing="0" cellpadding="6">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
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

            <p align="right"><strong>Total: Rp {{ number_format($transaction->total) }}</strong></p>
        </div>

        <!-- ================= THERMAL 80MM ================= -->
        <div class="receipt-thermal-80 thermal">
            <div class="center">
                @if($logo)
                    <img src="{{ $logo }}" style="max-width:60mm"><br>
                @endif
                <strong>{{ $store['name'] }}</strong><br>
                {{ $store['phone'] }}
            </div>

            <hr>

            Invoice: {{ $transaction->invoice_number }}<br>
            {{ $transaction->created_at->format('d/m/Y H:i') }}

            <hr>

            @foreach($transaction->items as $item)
                {{ $item->product?->name }}<br>
                {{ $item->quantity }} x {{ number_format($item->price) }}
                <div style="text-align:right">{{ number_format($item->total) }}</div>
            @endforeach

            <hr>
            TOTAL: {{ number_format($transaction->total) }}
        </div>

        <!-- ================= THERMAL 58MM ================= -->
        <div class="receipt-thermal-58 thermal">
            <div class="center">
                <strong>{{ $store['name'] }}</strong><br>
                {{ $store['phone'] }}
            </div>

            <hr>

            {{ $transaction->invoice_number }}<br>

            @foreach($transaction->items as $item)
                {{ Str::limit($item->product?->name,20) }}<br>
                {{ $item->quantity }} x {{ number_format($item->price) }}
                <div style="text-align:right">{{ number_format($item->total) }}</div>
            @endforeach

            <hr>
            TOTAL<br>
            {{ number_format($transaction->total) }}
        </div>

    </div>
</div>

<script>
document.querySelectorAll('[data-format]').forEach(btn=>{
    btn.onclick=()=>{
        document.querySelector('[data-layout]').dataset.format=btn.dataset.format;
        if(btn.dataset.print) window.print();
    }
});
</script>
</x-app-layout>
