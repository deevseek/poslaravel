<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PurchaseItem::with(['purchase', 'product']);

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($query) use ($search): void {
                $query->where('purchase_id', 'like', "%{$search}%")
                    ->orWhere('product_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('purchase_id')) {
            $query->where('purchase_id', $request->input('purchase_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $perPage = $request->integer('per_page', 15);
        $items = $query->paginate($perPage);

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
            'links' => [
                'first' => $items->url(1),
                'last' => $items->url($items->lastPage()),
                'prev' => $items->previousPageUrl(),
                'next' => $items->nextPageUrl(),
            ],
        ]);
    }

    public function show(PurchaseItem $purchaseItem): JsonResponse
    {
        $purchaseItem->load(['purchase', 'product']);

        return response()->json(['data' => $purchaseItem]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_id' => ['required', 'exists:purchases,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['subtotal'] = $validated['quantity'] * $validated['price'];

        $purchaseItem = PurchaseItem::create($validated);

        return response()->json([
            'data' => $purchaseItem->load(['purchase', 'product']),
        ], 201);
    }

    public function update(Request $request, PurchaseItem $purchaseItem): JsonResponse
    {
        $validated = $request->validate([
            'purchase_id' => ['sometimes', 'exists:purchases,id'],
            'product_id' => ['sometimes', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $quantity = $validated['quantity'] ?? $purchaseItem->quantity;
        $price = $validated['price'] ?? $purchaseItem->price;
        $validated['subtotal'] = $quantity * $price;

        $purchaseItem->update($validated);

        return response()->json([
            'data' => $purchaseItem->fresh(['purchase', 'product']),
        ]);
    }

    public function destroy(PurchaseItem $purchaseItem): JsonResponse
    {
        $purchaseItem->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
