<x-app-layout :title="'Tanda Terima Servis'">

<style>
/* =================================================
   PRINT ISOLATION (STABIL)
================================================== */
@media print {
  body * {
    visibility: hidden !important;
  }

  .print-area,
  .print-area * {
    visibility: visible !important;
  }

  body {
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
  }

  * {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
}

/* =================================================
   PAGE
================================================== */
@media print {
  @page {
    size: A4;
    margin: 0;
  }
}

/* =================================================
   PRINT FRAME (KUNCI UTAMA)
================================================== */
@media print {
  .print-area {
    position: relative; /* WAJIB */
  }

  .print-area[data-format="a5"] {
    width: 210mm;
    height: 148.5mm; /* SETENGAH A4 */
    margin: 0;
    box-sizing: border-box;
  }

  .print-area[data-format="thermal-80"] {
    width: 72mm;
  }

  .print-area[data-format="thermal-58"] {
    width: 48mm;
  }
}

/* =================================================
   BASE RECEIPT
================================================== */
.receipt {
  position: relative;
  width: 100%;
  padding: 12mm;
  font-family: "Courier New", monospace;
  font-size: 11px;
  color: #000;
  background: #fff;
}

.print-area[data-format^="thermal"] .receipt {
  padding: 4mm;
  font-size: 10px;
}

/* =================================================
   LAYOUT SWITCH
================================================== */
.receipt-thermal { display: none; }

.print-area[data-format^="thermal"] .receipt-standard {
  display: none;
}

.print-area[data-format^="thermal"] .receipt-thermal {
  display: block;
}

/* =================================================
   TYPOGRAPHY
================================================== */
.hr {
  border-top: 1.5px solid #000;
  margin: 8px 0;
}

.hr-dotted {
  border-bottom: 1.5px dotted #000;
  margin: 8px 0;
}

.row {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.col {
  width: 48%;
}

.label {
  display: inline-block;
  width: 130px;
}

.small {
  font-size: 10px;
}

/* =================================================
   WATERMARK (AMAN)
================================================== */
.watermark {
  position: absolute;
  top: 20%;
  left: 50%;
  transform: translateX(-50%) rotate(-25deg);
  font-size: 64px;
  font-weight: bold;
  color: rgba(0,0,0,0.15);
  z-index: 1;
  pointer-events: none;
}

.receipt-standard,
.receipt-thermal {
  position: relative;
  z-index: 2;
}

/* =================================================
   LOGO & QR
================================================== */
.logo {
  width: 42px;
  height: 42px;
  object-fit: contain;
}

.qr {
  width: 90px;
  height: 90px;
}

.print-area[data-format^="thermal"] .qr {
  width: 60px;
  height: 60px;
}

/* =================================================
   SIGN
================================================== */
.sign {
  margin-top: 28px;
  text-align: center;
}

.sign-line {
  margin-top: 22px;
  border-top: 1.5px solid #000;
}

/* =================================================
   NO PRINT
================================================== */
@media print {
  .no-print {
    display: none !important;
  }
}
</style>

@php
  $logoUrl = $store['logo']
    ? (\Illuminate\Support\Str::startsWith($store['logo'], ['http://','https://'])
        ? $store['logo']
        : asset($store['logo']))
    : null;
@endphp

{{-- ACTION --}}
<div class="no-print mb-3 flex gap-2">
  <button onclick="setFormat('a5')" class="border px-3 py-1">A5</button>
  <button onclick="setFormat('thermal-80')" class="border px-3 py-1">Thermal 80</button>
  <button onclick="setFormat('thermal-58')" class="border px-3 py-1">Thermal 58</button>
  <button onclick="window.print()" class="bg-black text-white px-4 py-1">Cetak</button>
</div>

<div class="print-area" data-format="a5">

  <div class="receipt">
    <div class="watermark">{{ strtoupper($store['name']) }}</div>

    {{-- ================= STANDARD ================= --}}
    <div class="receipt-standard">

      <div class="row">
        <div>
          @if($logoUrl)
            <img src="{{ $logoUrl }}" class="logo">
          @endif
          <strong>{{ $store['name'] }}</strong><br>
          {{ $store['address'] }}<br>
          Telp: {{ $store['phone'] }}
        </div>

        <div style="text-align:right">
          <strong>TANDA TERIMA SERVIS</strong><br><br>
          No: svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}<br>
          Tgl: {{ $service->created_at->format('d M Y') }}<br>
          Nama: {{ $service->customer->name }}<br>
          Telp: {{ $service->customer->phone }}
        </div>
      </div>

      <div class="hr"></div>

      <strong>DATA BARANG SERVIS</strong>
      <div class="hr-dotted"></div>

      <div class="row">
        <div class="col">
          <div><span class="label">Barang</span>: {{ $service->device }}</div>
          <div><span class="label">Model</span>: {{ $service->model ?? '-' }}</div>
          <div><span class="label">Serial</span>: {{ $service->serial_number ?? '-' }}</div>
          <div><span class="label">Kelengkapan</span>: {{ $service->accessories ?? '-' }}</div>
          <div><span class="label">Keluhan</span>: {{ $service->complaint }}</div>
          <div><span class="label">DP</span>: Rp {{ number_format($service->deposit ?? 0,0,',','.') }}</div>
        </div>
        <div class="col">
          Estimasi Biaya
          <div class="hr-dotted"></div>
          <div class="hr-dotted"></div>
          Keterangan
          <div class="hr-dotted"></div>
          <div class="hr-dotted"></div>
        </div>
      </div>

      <div class="hr"></div>

      <div class="row text-center">
        <div>
          <img src="{{ $progressQrUrl }}" class="qr"><br>
          <span class="small">Update</span>
        </div>
        <div>
          <img src="{{ $trackingQrUrl ?? $progressQrUrl }}" class="qr"><br>
          <span class="small">Tracking</span>
        </div>
      </div>

      <div class="hr"></div>

      <div class="small">
        * Nota dibawa saat pengambilan barang<br>
        * Dicetak: {{ now()->format('d/m/Y H:i') }}
      </div>

      <div class="row sign">
        <div class="col">
          Penerima
          <div class="sign-line"></div>
        </div>
        <div class="col">
          Pemilik
          <div class="sign-line"></div>
        </div>
      </div>

    </div>

    {{-- ================= THERMAL ================= --}}
    <div class="receipt-thermal">
      <div style="text-align:center;font-weight:bold">
        {{ strtoupper($store['name']) }}
      </div>
      <div style="text-align:center;font-size:9px">
        {{ $store['address'] }}<br>
        {{ $store['phone'] }}
      </div>

      <div class="hr-dotted"></div>

      <div>No: svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</div>
      <div>Nama: {{ $service->customer->name }}</div>
      <div>Barang: {{ $service->device }}</div>
      <div>Keluhan: {{ $service->complaint }}</div>

      <div class="hr-dotted"></div>

      <div class="row">
        <img src="{{ $progressQrUrl }}" class="qr">
        <img src="{{ $trackingQrUrl ?? $progressQrUrl }}" class="qr">
      </div>
    </div>

  </div>
</div>

<script>
function setFormat(format) {
  document.querySelector('.print-area').dataset.format = format;
}
</script>

</x-app-layout>
