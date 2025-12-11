<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
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
            'complaint' => ['required', 'string'],
            'service_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        $service = Service::create([
            'customer_id' => $validated['customer_id'],
            'device' => $validated['device'],
            'complaint' => $validated['complaint'],
            'service_fee' => $validated['service_fee'] ?? 0,
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

        try {
            DB::transaction(function () use ($validated, $service) {
                if ($service->status === $validated['status']) {
                    return;
                }

                $service->update(['status' => $validated['status']]);
                $service->addLog('Status diubah menjadi ' . $validated['status']);

                if ($validated['status'] === Service::STATUS_DIAMBIL) {
                    $this->createTransaction($service);
                }
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('success', 'Status service diperbarui.');
    }

    protected function createTransaction(Service $service): void
    {
        if ($service->transaction_id) {
            return;
        }

        $items = $service->items()->with('product')->get();
        $itemsTotal = $items->sum('total');
        $subtotal = $itemsTotal + (float) $service->service_fee;

        $transaction = Transaction::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $subtotal,
            'payment_method' => 'cash',
            'paid_amount' => $subtotal,
            'change_amount' => 0,
        ]);

        foreach ($items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'discount' => 0,
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
                'total' => $service->service_fee,
            ]);
        }

        $service->update(['transaction_id' => $transaction->id]);
        $service->addLog('Transaksi POS otomatis dibuat: ' . $transaction->invoice_number);
    }

    protected function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Transaction::whereDate('created_at', now()->toDateString())->count() + 1;

        return 'SRV-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
