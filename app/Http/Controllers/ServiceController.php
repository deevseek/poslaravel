<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Finance;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\WaTemplate;
use App\Models\Warranty;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private WhatsAppService $whatsAppService)
    {
    }

    public function index(): View
    {
        $services = Service::with('customer')->latest()->paginate(10);

        return view('services.index', compact('services'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();

        return view('services.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'device' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'accessories' => ['nullable', 'string'],
            'complaint' => ['required', 'string'],
            'service_fee' => ['nullable', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
        ]);

        $service = Service::create([
            'customer_id' => $validated['customer_id'],
            'device' => $validated['device'],
            'serial_number' => $validated['serial_number'] ?? null,
            'accessories' => $validated['accessories'] ?? null,
            'complaint' => $validated['complaint'],
            'service_fee' => $validated['service_fee'] ?? 0,
            'warranty_days' => $validated['warranty_days'] ?? 0,
            'status' => Service::STATUS_MENUNGGU,
        ]);

        $service->addLog('Service dibuat');

        return redirect()->route('services.show', $service)->with('success', 'Service berhasil dicatat.');
    }

    public function show(Service $service): View
    {
        $service->load([
            'customer',
            'items.product',
            'logs.user',
            'transaction',
        ]);

        $products = Product::orderBy('name')->get();

        return view('services.show', [
            'service' => $service,
            'products' => $products,
            'statuses' => Service::STATUSES,
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'diagnosis' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'service_fee' => ['required', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
        ]);

        $service->update($validated);
        $service->addLog('Diagnosa/biaya diperbarui');

        return back()->with('success', 'Data service diperbarui.');
    }

    public function addItem(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($validated, $service) {
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

                if ($product->stock < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok produk tidak mencukupi.',
                    ]);
                }

                $lineTotal = $product->price * $validated['quantity'];

                ServiceItem::create([
                    'service_id' => $service->id,
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'price' => $product->price,
                    'total' => $lineTotal,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_OUT,
                    'quantity' => $validated['quantity'],
                    'note' => 'Penggunaan untuk service #' . $service->id,
                ]);

                $service->addLog('Menambahkan sparepart: ' . $product->name . ' x ' . $validated['quantity']);
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('success', 'Sparepart ditambahkan.');
    }

    public function updateStatus(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Service::STATUSES)],
        ]);

        $statusChanged = false;

        try {
            DB::transaction(function () use (&$statusChanged, $validated, $service) {
                if ($service->status === $validated['status']) {
                    return;
                }

                $service->update(['status' => $validated['status']]);
                $service->addLog('Status diubah menjadi ' . $validated['status']);
                $statusChanged = true;

                if ($validated['status'] === Service::STATUS_SELESAI) {
                    $this->createServiceWarranty($service);
                }

                if ($validated['status'] === Service::STATUS_DIAMBIL) {
                    $this->createTransaction($service);
                }
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        if ($statusChanged) {
            $this->sendServiceStatusNotification($service->fresh(['customer']));
        }

        return back()->with('success', 'Status service diperbarui.');
    }

    protected function createTransaction(Service $service): void
    {
        $items = $service->items()->with('product')->get();
        $itemsTotal = $items->sum('total');
        $subtotal = $itemsTotal + (float) $service->service_fee;
        $totalHpp = 0;

        $transaction = Transaction::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'customer_id' => $service->customer_id,
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $subtotal,
            'payment_method' => 'cash',
            'paid_amount' => $subtotal,
            'change_amount' => 0,
        ]);

        foreach ($items as $item) {
            $hpp = $item->product?->cost_price ?? 0;
            $subtotalHpp = $hpp * $item->quantity;
            $totalHpp += $subtotalHpp;

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'discount' => 0,
                'hpp' => $hpp,
                'subtotal_hpp' => $subtotalHpp,
                'total' => $item->total,
            ]);
        }

        if ($service->service_fee > 0) {
            $categoryId = Category::first()?->id;

            if (! $categoryId) {
                $categoryId = Category::create(['name' => 'Layanan'])->id;
            }

            $placeholderProduct = Product::firstOrCreate(
                ['sku' => 'SERVICE-FEE'],
                [
                    'category_id' => $categoryId,
                    'name' => 'Jasa Service',
                    'price' => 0,
                    'stock' => 0,
                ]
            );

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $placeholderProduct->id,
                'quantity' => 1,
                'price' => $service->service_fee,
                'discount' => 0,
                'hpp' => 0,
                'subtotal_hpp' => 0,
                'total' => $service->service_fee,
            ]);
        }

        $service->update(['transaction_id' => $transaction->id]);
        $service->addLog('Transaksi POS otomatis dibuat: ' . $transaction->invoice_number);

        Finance::create([
            'type' => 'income',
            'category' => 'Service',
            'nominal' => $subtotal,
            'note' => 'Pembayaran service - ' . $transaction->invoice_number,
            'recorded_at' => $transaction->created_at->toDateString(),
            'source' => 'service',
            'reference_id' => $service->id,
            'reference_type' => 'service',
            'created_by' => auth()->id(),
        ]);

        if ($totalHpp > 0) {
            Finance::create([
                'type' => 'expense',
                'category' => 'HPP',
                'nominal' => $totalHpp,
                'note' => 'HPP service - ' . $transaction->invoice_number,
                'recorded_at' => $transaction->created_at->toDateString(),
                'source' => 'service',
                'reference_id' => $service->id,
                'reference_type' => 'service',
                'created_by' => auth()->id(),
            ]);
        }
    }

    protected function createServiceWarranty(Service $service): void
    {
        if ($service->warranty_days <= 0) {
            return;
        }

        if (Warranty::where('type', Warranty::TYPE_SERVICE)->where('reference_id', $service->id)->exists()) {
            return;
        }

        Warranty::create([
            'type' => Warranty::TYPE_SERVICE,
            'reference_id' => $service->id,
            'customer_id' => $service->customer_id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($service->warranty_days)->toDateString(),
            'description' => 'Garansi layanan untuk ' . $service->device,
            'status' => Warranty::STATUS_ACTIVE,
        ]);
    }

    protected function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Transaction::whereDate('created_at', now()->toDateString())->count() + 1;
        $prefix = Setting::getValue(Setting::TRANSACTION_PREFIX, 'SRV');
        $padding = (int) Setting::getValue(Setting::TRANSACTION_PADDING, 4);

        return $prefix . '-' . $date . '-' . str_pad((string) $count, $padding, '0', STR_PAD_LEFT);
    }

    protected function sendServiceStatusNotification(Service $service): void
    {
        $customer = $service->customer;

        if (! $customer || ! $customer->phone) {
            return;
        }

        $template = WaTemplate::where('code', 'service_' . $service->status)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return;
        }

        $storeName = Setting::getValue(Setting::STORE_NAME, config('app.name'));

        $message = str_replace(
            ['{{nama}}', '{{device}}', '{{status}}', '{{nama_toko}}'],
            [$customer->name, $service->device, $service->status, $storeName],
            $template->message
        );

        $this->whatsAppService->sendMessage($customer->phone, $message, 'service');
    }
}
