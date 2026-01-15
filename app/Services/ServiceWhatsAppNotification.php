<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;

class ServiceWhatsAppNotification
{
    public function __construct(private WhatsAppService $whatsApp)
    {
    }

    public function forService(Service $service): array
    {
        $storeName = Setting::getValue(Setting::STORE_NAME, config('app.name'));
        $storePhone = Setting::getValue(Setting::STORE_PHONE);
        $serviceNumber = $this->formatServiceNumber($service);
        $progressUrl = URL::signedRoute('services.progress', $service);
        $totalService = $service->totalItems() + (float) $service->service_fee;
        $warrantyDays = (int) $service->warranty_days;
        $invoiceNumber = $service->transaction?->invoice_number;

        $diagnosis = $service->diagnosis ?: '-';
        $notes = $service->notes ?: '-';

        $messages = [
            [
                'key' => 'created',
                'label' => 'Notifikasi Service Diterima',
                'message' => "Halo {$service->customer->name},\n\nTerima kasih sudah melakukan service di {$storeName}.\nService Anda sudah kami terima dengan nomor {$serviceNumber}.\nPerangkat: {$service->device}\nKeluhan: {$service->complaint}\n\nPantau progres service di: {$progressUrl}\n\nSalam,\n{$storeName}",
            ],
            [
                'key' => 'diagnosis',
                'label' => 'Notifikasi Diagnosa & Biaya',
                'message' => "Halo {$service->customer->name},\n\nUpdate diagnosa/biaya untuk service {$serviceNumber}.\nDiagnosa: {$diagnosis}\nCatatan: {$notes}\nBiaya jasa: Rp " . $this->formatCurrency($service->service_fee) . "\n\nJika ada pertanyaan, silakan hubungi kami.\n{$storeName}",
            ],
            [
                'key' => 'in_progress',
                'label' => 'Notifikasi Service Dikerjakan',
                'message' => "Halo {$service->customer->name},\n\nUpdate service {$serviceNumber}: perangkat sedang dikerjakan.\nDiagnosa: {$diagnosis}\nEstimasi biaya jasa: Rp " . $this->formatCurrency($service->service_fee) . "\n\nKami akan mengabari setelah selesai.\n{$storeName}",
            ],
            [
                'key' => 'done',
                'label' => 'Notifikasi Service Selesai',
                'message' => "Halo {$service->customer->name},\n\nService {$serviceNumber} sudah selesai.\nTotal biaya: Rp " . $this->formatCurrency($totalService) . "\nGaransi: {$warrantyDays} hari\n\nSilakan ambil perangkat Anda di toko.\n{$storeName}",
            ],
            [
                'key' => 'picked_up',
                'label' => 'Notifikasi Service Diambil',
                'message' => "Halo {$service->customer->name},\n\nTerima kasih. Service {$serviceNumber} sudah diambil." . ($invoiceNumber ? "\nInvoice: {$invoiceNumber}" : '') . "\n\nSemoga perangkat berfungsi normal. Jika ada kendala, silakan hubungi kami.\n{$storeName}",
            ],
        ];

        $phone = $service->customer?->phone;

        $notifications = array_map(function (array $item) use ($phone) {
            $item['link'] = $this->whatsApp->buildLink($phone, $item['message']);

            return $item;
        }, $messages);

        return [
            'phone' => $phone,
            'store_phone' => $storePhone,
            'items' => $notifications,
        ];
    }

    private function formatServiceNumber(Service $service): string
    {
        return 'svc/' . $service->created_at->format('Y') . '/' . $service->id;
    }

    private function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}
