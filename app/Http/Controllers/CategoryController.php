<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category): View
    {
        $category->loadCount('products');

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');

        if (! $handle) {
            return redirect()->route('categories.index')->with('csv_errors', ['Gagal membaca file CSV.']);
        }

        $headerRow = fgetcsv($handle);

        if (! $headerRow) {
            fclose($handle);

            return redirect()->route('categories.index')->with('csv_errors', ['Header CSV tidak ditemukan.']);
        }

        $headerMap = $this->mapCsvHeaders($headerRow);

        if (! in_array('name', $headerMap, true)) {
            fclose($handle);

            return redirect()->route('categories.index')->with('csv_errors', ['Kolom "name" wajib ada pada CSV.']);
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

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $validated = $validator->validated();

            $category = Category::updateOrCreate(
                ['name' => $validated['name']],
                ['description' => $validated['description'] ?? null]
            );

            if ($category->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        fclose($handle);

        $redirect = redirect()->route('categories.index');

        if ($created > 0 || $updated > 0) {
            $messages = [];

            if ($created > 0) {
                $messages[] = "{$created} kategori ditambahkan";
            }

            if ($updated > 0) {
                $messages[] = "{$updated} kategori diperbarui";
            }

            $redirect = $redirect->with('csv_success', implode(', ', $messages) . '.');
        }

        if (! empty($errors)) {
            $redirect = $redirect->with('csv_errors', $errors);
        }

        return $redirect;
    }

    private function mapCsvHeaders(array $headers): array
    {
        return array_map(function (?string $header) {
            $normalized = $this->normalizeCsvHeader($header ?? '');

            return match ($normalized) {
                'nama' => 'name',
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
