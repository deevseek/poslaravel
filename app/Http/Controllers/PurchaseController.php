<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $purchases = Purchase::with(['supplier', 'items.product'])->latest()->paginate(10);

        return view('purchases.index', compact('suppliers', 'products', 'purchases'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'payment_status' => ['required', 'in:' . implode(',', [Purchase::STATUS_PAID, Purchase::STATUS_DEBT])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $items = collect($validated['items'])->map(function ($item) {
                $quantity = (int) $item['quantity'];
                $price = (float) $item['price'];

                return array_merge($item, [
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $quantity * $price,
                ]);
            });

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $this->generateInvoiceNumber(),
                'purchase_date' => $validated['purchase_date'],
                'payment_status' => $validated['payment_status'],
                'total_amount' => $items->sum('subtotal'),
                'notes' => $validated['notes'] ?? null,
            ]);

            $items->each(function ($item) use ($purchase) {
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                StockMovement::create([
                    'product_id' => $purchaseItem->product_id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $purchaseItem->quantity,
                    'note' => 'Pembelian ' . $purchase->invoice_number,
                ]);
            });
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dicatat dan stok telah diperbarui.');
    }

    protected function generateInvoiceNumber(): string
    {
        $prefix = Setting::getValue(Setting::TRANSACTION_PREFIX, 'PB');
        $date = now()->format('Ymd');
        $countToday = Purchase::whereDate('created_at', now()->toDateString())->count() + 1;
        $padding = (int) Setting::getValue(Setting::TRANSACTION_PADDING, 4);

        return $prefix . '-' . $date . '-' . str_pad((string) $countToday, $padding, '0', STR_PAD_LEFT);
    }
}
