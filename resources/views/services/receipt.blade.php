<x-app-layout :title="'Tanda Terima Servis'">

<style>
/* =================================================
   PRINT ISOLATION
================================================== */
@media print {
  body * { visibility: hidden !important; }
  .print-area, .print-area * { visibility: visible !important; }
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
   PAGE (DESKTOP PRINTER)
================================================== */
@media print {
  @page {
    size: Letter;
    margin: 0;
  }
}

/* =================================================
   PRINT FRAME
================================================== */
@media print {
  .print-area[data-format="a5"] {
    width: 210mm;
    height: 148mm;
  }
  .print-area[data-format="letter-half"] {
    width: 8.5in;
    height: 11in;
    overflow: hidden;
  }

  /* THERMAL */
  .print-area[data-format="thermal-80"] {
    width: 80mm;
  }
  .print-area[data-format="thermal-58"] {
    width: 58mm;
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

/* THERMAL ADJUST */
.print-area[data-format^="thermal"] .receipt {
  padding: 4mm;
  font-size: 10px;
}

/* =================================================
   TYPOGRAPHY & LINES
================================================== */
.title {
  text-align: center;
  font-weight: bold;
  letter-spacing: 3px;
  margin-bottom: 6px;
}

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
   WATERMARK (FIX: TIDAK HILANG DI PRINT)
================================================== */
.watermark {
  position: absolute;
  top: 18%;
  left: 50%;
  transform: translateX(-50%) rotate(-25deg);
  font-size: 64px;
  font-weight: bold;
  color: #000;
  opacity: 0.07;
  z-index: 0;
  white-space: nowrap;
  pointer-events: none;
}

/* =================================================
   LOGO & QR
================================================== */
.logo {
  width: 42px;
  height: 42px;
  object-fit: contain;
  display: block;
}

.qr {
  width: 90px;
  height: 90px;
}

/* THERMAL QR */
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
   NO PRINT ACTION
================================================== */
@media print {
  .no-print { display: none !important; }
}
</style>

@php
  $logoUrl = $store['logo']
    ? (\Illuminate\Support\Str::startsWith($store['logo'], ['http://','https://']) ? $store['logo'] : asset($store['logo']))
    : null;
@endphp

{{-- ACTION --}}
<div class="no-print mb-3 flex flex-wrap gap-2">
  <button onclick="setFormat('a5')" class="border px-3 py-1">A5</button>
  <button onclick="setFormat('letter-half')" class="border px-3 py-1">Letter ½</button>
  <button onclick="setFormat('thermal-80')" class="border px-3 py-1">Thermal 80mm</button>
  <button onclick="setFormat('thermal-58')" class="border px-3 py-1">Thermal 58mm</button>
  <button onclick="window.print()" class="bg-black text-white px-4 py-1">Cetak</button>
</div>

<div class="print-area relative" data-format="a5">

  <div class="watermark">{{ strtoupper($store['name']) }}</div>

  <div class="receipt">

    <div class="row">
      <div>
        @if($logoUrl)
          <img src="{{ $logoUrl }}" alt="Logo" class="logo">
        @endif
        <strong>{{ $store['name'] }}</strong><br>
        {{ $store['address'] }}<br>
        Telp: {{ $store['phone'] }}
      </div>
      <div style="text-align:right">
        <strong>TANDA TERIMA SERVIS</strong><br><br>
        No. Servis : svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}<br>
        Tanggal : {{ $service->created_at->format('d M Y') }}<br>
        Nama : {{ $service->customer->name }}<br>
        Telepon : {{ $service->customer->phone }}
      </div>
    </div>

    <div class="hr"></div>

    <strong>DATA BARANG SERVIS</strong>
    <div class="hr-dotted"></div>

    <div class="row">
      <div class="col">
        <div><span class="label">Nama Barang</span>: {{ $service->device }}</div>
        <div><span class="label">Model / Seri</span>: {{ $service->model ?? '-' }}</div>
        <div><span class="label">Nomor Serial</span>: {{ $service->serial_number ?? '-' }}</div>
        <div><span class="label">Kelengkapan</span>: {{ $service->accessories ?? '-' }}</div>
        <div><span class="label">Kerusakan</span>: {{ $service->complaint }}</div>
        <div><span class="label">DP / Uang Muka</span>: Rp {{ number_format($service->deposit ?? 0,0,',','.') }}</div>
      </div>
      <div class="col">
        Estimasi Biaya (Rp)
        <div class="hr-dotted"></div>
        <div class="hr-dotted"></div>

        Keterangan Lain-Lain
        <div class="hr-dotted"></div>
        <div class="hr-dotted"></div>
        <div class="hr-dotted"></div>
      </div>
    </div>

    <div class="hr"></div>

    <div class="row text-center">
      <div>
        <img src="{{ $progressQrUrl }}" class="qr"><br>
        <span class="small">Scan Update Status</span>
      </div>
      <div>
        <img src="{{ $trackingQrUrl ?? $progressQrUrl }}" class="qr"><br>
        <span class="small">Scan Tracking Servis</span>
      </div>
    </div>

    <div class="hr"></div>

    <div class="small">
      * Nota ini dibawa saat pengambilan barang.<br>
      * Dicetak: {{ now()->format('d/m/Y H:i') }}<br><br>

      <strong>Syarat & Ketentuan:</strong><br>
      1. Barang > 3 bulan tidak diambil bukan tanggung jawab kami.<br>
      2. Garansi tidak berlaku jika nota hilang.
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
</div>

<script>
function setFormat(format) {
  document.querySelector('.print-area').dataset.format = format;
}
</script>

</x-app-layout>
