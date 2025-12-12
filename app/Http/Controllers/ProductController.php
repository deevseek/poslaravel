<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::select([
            'id',
            'category_id',
            'sku',
            'name',
            'stock',
            'cost_price',
            'price',
            'pricing_mode',
            'margin_percentage',
        ])
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
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
        $shouldGenerateSku = !$request->filled('sku');

        if ($shouldGenerateSku) {
            $validated['sku'] = Product::generateSku($category);
        }

        $validated = $this->applyPricingRules($validated);

        $attempts = 0;

        while (true) {
            try {
                Product::create($validated);
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

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'stockMovements' => function ($query) {
            $query->latest()->limit(5);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
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

        $validated = $this->applyPricingRules($validated);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
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
}
