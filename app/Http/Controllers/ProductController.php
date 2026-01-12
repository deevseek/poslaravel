<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $search = request('search');

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
            ->when($search, function ($query) use ($search) {
                $keyword = mb_strtolower(trim($search));
                $like = '%' . $keyword . '%';

                $query->where(function ($subQuery) use ($like) {
                    $subQuery->whereRaw('LOWER(name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(sku) LIKE ?', [$like]);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

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

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');

        if (! $handle) {
            return redirect()->route('products.index')->with('csv_errors', ['Gagal membaca file CSV.']);
        }

        $headerRow = fgetcsv($handle);

        if (! $headerRow) {
            fclose($handle);

            return redirect()->route('products.index')->with('csv_errors', ['Header CSV tidak ditemukan.']);
        }

        $headerMap = $this->mapCsvHeaders($headerRow);
        $requiredHeaders = ['name', 'category', 'stock'];
        $missingHeaders = array_diff($requiredHeaders, $headerMap);

        if (! empty($missingHeaders)) {
            fclose($handle);
            $missingList = implode(', ', $missingHeaders);

            return redirect()->route('products.index')->with('csv_errors', ["Kolom CSV wajib: {$missingList}."]);
        }

        $errors = [];
        $created = 0;
        $updated = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $data = $this->extractCsvRow($headerMap, $row);

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $data['pricing_mode'] = $this->normalizePricingMode($data['pricing_mode'] ?? null);

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'category' => ['required', 'string', 'max:255'],
                'sku' => ['nullable', 'string', 'max:255'],
                'cost_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    Rule::requiredIf(($data['pricing_mode'] ?? Product::PRICING_MODE_MANUAL) === Product::PRICING_MODE_PERCENTAGE),
                    Rule::when(($data['pricing_mode'] ?? Product::PRICING_MODE_MANUAL) === Product::PRICING_MODE_PERCENTAGE, ['gt:0']),
                ],
                'price' => ['required_if:pricing_mode,' . Product::PRICING_MODE_MANUAL, 'nullable', 'numeric', 'min:0'],
                'pricing_mode' => ['required', 'in:' . implode(',', [Product::PRICING_MODE_MANUAL, Product::PRICING_MODE_PERCENTAGE])],
                'margin_percentage' => ['required_if:pricing_mode,' . Product::PRICING_MODE_PERCENTAGE, 'nullable', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
                'warranty_days' => ['nullable', 'integer', 'min:0'],
                'description' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $validated = $validator->validated();
            $category = Category::firstOrCreate(['name' => $validated['category']]);
            $payload = [
                'category_id' => $category->id,
                'name' => $validated['name'],
                'sku' => $validated['sku'] ?? null,
                'cost_price' => $validated['cost_price'] ?? null,
                'price' => $validated['price'] ?? null,
                'pricing_mode' => $validated['pricing_mode'],
                'margin_percentage' => $validated['margin_percentage'] ?? null,
                'stock' => $validated['stock'],
                'warranty_days' => $validated['warranty_days'] ?? null,
                'description' => $validated['description'] ?? null,
            ];

            $payload = $this->applyPricingRules($payload);
            $shouldGenerateSku = empty($payload['sku']);

            if (! $shouldGenerateSku) {
                $product = Product::where('sku', $payload['sku'])->first();

                if ($product) {
                    $product->update($payload);
                    $updated++;
                    continue;
                }
            }

            if ($shouldGenerateSku) {
                $payload['sku'] = Product::generateSku($category);
            }

            $attempts = 0;

            while (true) {
                try {
                    Product::create($payload);
                    $created++;
                    break;
                } catch (QueryException $exception) {
                    if ($shouldGenerateSku && $this->isUniqueConstraintViolation($exception) && $attempts < 3) {
                        $payload['sku'] = Product::generateSku($category);
                        $attempts++;
                        continue;
                    }

                    $errors[] = "Baris {$rowNumber}: Gagal menyimpan produk.";
                    break;
                }
            }
        }

        fclose($handle);

        $redirect = redirect()->route('products.index');

        if ($created > 0 || $updated > 0) {
            $messages = [];

            if ($created > 0) {
                $messages[] = "{$created} produk ditambahkan";
            }

            if ($updated > 0) {
                $messages[] = "{$updated} produk diperbarui";
            }

            $redirect = $redirect->with('csv_success', implode(', ', $messages) . '.');
        }

        if (! empty($errors)) {
            $redirect = $redirect->with('csv_errors', $errors);
        }

        return $redirect;
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

    private function normalizePricingMode(?string $mode): string
    {
        if (! $mode) {
            return Product::PRICING_MODE_MANUAL;
        }

        $normalized = Str::of($mode)->lower()->trim()->toString();

        return match ($normalized) {
            'persentase' => Product::PRICING_MODE_PERCENTAGE,
            'manual' => Product::PRICING_MODE_MANUAL,
            default => $normalized,
        };
    }

    private function mapCsvHeaders(array $headers): array
    {
        return array_map(function (?string $header) {
            $normalized = $this->normalizeCsvHeader($header ?? '');

            return match ($normalized) {
                'kategori' => 'category',
                'category_name' => 'category',
                'nama' => 'name',
                'harga' => 'price',
                'hpp' => 'cost_price',
                'mode_harga' => 'pricing_mode',
                'margin' => 'margin_percentage',
                'stok' => 'stock',
                'garansi' => 'warranty_days',
                'deskripsi' => 'description',
                default => $normalized,
            };
        }, $headers);
    }

    private function normalizeCsvHeader(string $header): string
    {
        return Str::of($header)
            ->lower()
            ->trim()
            ->replace([' ', '-'], '_')
            ->replaceMatches('/[^a-z0-9_]/', '')
            ->toString();
    }

    private function extractCsvRow(array $headerMap, array $row): array
    {
        $data = [];

        foreach ($headerMap as $index => $key) {
            if ($key === '') {
                continue;
            }

            $value = $row[$index] ?? null;
            $value = is_string($value) ? trim($value) : $value;
            $data[$key] = $value === '' ? null : $value;
        }

        return $data;
    }

    private function isEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }
}
