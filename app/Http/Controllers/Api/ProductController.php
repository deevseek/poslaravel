<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $perPage = $request->integer('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
        ]);
    }

    public function show(Product $product)
    {
        $product->load('category');

        return response()->json(['data' => $product]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->input('pricing_mode') === Product::PRICING_MODE_PERCENTAGE),
                Rule::when($request->input('pricing_mode') === Product::PRICING_MODE_PERCENTAGE, ['gt:0']),
            ],
            'price' => ['required_if:pricing_mode,' . Product::PRICING_MODE_MANUAL, 'nullable', 'numeric', 'min:0'],
            'pricing_mode' => ['required', 'in:' . implode(',', [Product::PRICING_MODE_MANUAL, Product::PRICING_MODE_PERCENTAGE])],
            'margin_percentage' => ['required_if:pricing_mode,' . Product::PRICING_MODE_PERCENTAGE, 'nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $shouldGenerateSku = ! $request->filled('sku');

        if ($shouldGenerateSku) {
            $validated['sku'] = Product::generateSku($category);
        }

        $validated = $this->applyPricingRules($validated);

        $attempts = 0;

        while (true) {
            try {
                $product = Product::create($validated);
                break;
            } catch (QueryException $exception) {
                if ($shouldGenerateSku && $this->isUniqueConstraintViolation($exception) && $attempts < 3) {
                    $validated['sku'] = Product::generateSku($category);
                    $attempts++;
                    continue;
                }

                throw $exception;
            }
        }

        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'cost_price' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf($request->input('pricing_mode') === Product::PRICING_MODE_PERCENTAGE),
                Rule::when($request->input('pricing_mode') === Product::PRICING_MODE_PERCENTAGE, ['gt:0']),
            ],
            'price' => ['required_if:pricing_mode,' . Product::PRICING_MODE_MANUAL, 'nullable', 'numeric', 'min:0'],
            'pricing_mode' => ['sometimes', 'in:' . implode(',', [Product::PRICING_MODE_MANUAL, Product::PRICING_MODE_PERCENTAGE])],
            'margin_percentage' => ['required_if:pricing_mode,' . Product::PRICING_MODE_PERCENTAGE, 'nullable', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'warranty_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $payload = $this->applyPricingRulesForUpdate($product, $validated);

        $product->update($payload);

        return response()->json(['data' => $product->fresh('category')]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Deleted.']);
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return $exception->getCode() === '23000';
    }

    private function applyPricingRules(array $validated): array
    {
        $validated['cost_price'] = $validated['cost_price'] ?? 0;

        if ($validated['pricing_mode'] === Product::PRICING_MODE_MANUAL) {
            $validated['margin_percentage'] = null;
        } else {
            $validated['price'] = Product::calculateSellingPrice(
                (float) $validated['cost_price'],
                (float) $validated['margin_percentage']
            );
        }

        return $validated;
    }

    private function applyPricingRulesForUpdate(Product $product, array $validated): array
    {
        $pricingMode = $validated['pricing_mode'] ?? $product->pricing_mode;
        $costPrice = $validated['cost_price'] ?? $product->cost_price ?? 0;
        $margin = $validated['margin_percentage'] ?? $product->margin_percentage;

        if ($pricingMode === Product::PRICING_MODE_MANUAL) {
            $validated['margin_percentage'] = null;
        } elseif ($margin !== null) {
            $validated['price'] = Product::calculateSellingPrice(
                (float) $costPrice,
                (float) $margin
            );
        }

        return $validated;
    }
}
