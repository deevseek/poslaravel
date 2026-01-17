<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // GET /api/v1/purchases
    public function index(Request $request): JsonResponse
    {
        $query = Purchase::with(['supplier', 'items.product'])->latest();

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where('invoice_number', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%")
                ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                    $supplierQuery->where('name', 'like', "%{$search}%");
                });
        }

        $perPage = $request->integer('per_page', 15);
        $purchases = $query->paginate($perPage);

        return response()->json([
            'data' => $purchases->items(),
            'meta' => [
                'current_page' => $purchases->currentPage(),
                'last_page' => $purchases->lastPage(),
                'per_page' => $purchases->perPage(),
                'total' => $purchases->total(),
                'from' => $purchases->firstItem(),
                'to' => $purchases->lastItem(),
            ],
            'links' => [
                'first' => $purchases->url(1),
                'last' => $purchases->url($purchases->lastPage()),
                'prev' => $purchases->previousPageUrl(),
                'next' => $purchases->nextPageUrl(),
            ],
        ]);
    }

    // GET /api/v1/purchases/{id}
    public function show(Purchase $purchase): JsonResponse
    {
        $purchase->load(['supplier', 'items.product']);

        return response()->json(['data' => $purchase]);
    }

    // POST /api/v1/purchases
    public function store(Request $request): JsonResponse
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

        $purchase = DB::transaction(function () use ($validated) {
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
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $currentStock = (int) $product->stock;
                $existingAvgCost = (float) ($product->avg_cost ?? 0);
                $newStockTotal = $currentStock + $purchaseItem->quantity;
                $newAvgCost = $newStockTotal > 0
                    ? (($currentStock * $existingAvgCost) + ($purchaseItem->quantity * $purchaseItem->price)) / $newStockTotal
                    : $purchaseItem->price;

                $product->avg_cost = round($newAvgCost, 2);
                $product->cost_price = $product->avg_cost;

                if (
                    $product->pricing_mode === Product::PRICING_MODE_PERCENTAGE &&
                    ! is_null($product->margin_percentage)
                ) {
                    $product->price = Product::calculateSellingPrice(
                        (float) $product->cost_price,
                        (float) $product->margin_percentage
                    );
                }

                $product->save();

                StockMovement::create([
                    'product_id' => $purchaseItem->product_id,
                    'type' => StockMovement::TYPE_IN,
                    'source' => 'purchase',
                    'reference' => $purchase->id,
                    'quantity' => $purchaseItem->quantity,
                    'note' => 'Pembelian Supplier - ' . $purchase->invoice_number,
                ]);
            });

            return $purchase;
        });

        $purchase->load(['supplier', 'items.product']);

        return response()->json(['data' => $purchase], 201);
    }

    // PATCH /api/v1/purchases/{id}
    public function update(Request $request, Purchase $purchase): JsonResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['sometimes', 'exists:suppliers,id'],
            'purchase_date' => ['sometimes', 'date'],
            'payment_status' => ['sometimes', 'in:' . implode(',', [Purchase::STATUS_PAID, Purchase::STATUS_DEBT])],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $purchase->update($validated);
        $purchase->load(['supplier', 'items.product']);

        return response()->json(['data' => $purchase]);
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
