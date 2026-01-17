<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::orderBy('name')->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');

        if (! $handle) {
            return redirect()->route('customers.index')->with('csv_errors', ['Gagal membaca file CSV.']);
        }

        $headerRow = fgetcsv($handle);

        if (! $headerRow) {
            fclose($handle);

            return redirect()->route('customers.index')->with('csv_errors', ['Header CSV tidak ditemukan.']);
        }

        $headerMap = $this->mapCsvHeaders($headerRow);

        if (! in_array('name', $headerMap, true)) {
            fclose($handle);

            return redirect()->route('customers.index')->with('csv_errors', ['Kolom "name" wajib ada pada CSV.']);
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
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $validated = $validator->validated();

            if (! empty($validated['email'])) {
                $customer = Customer::updateOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => $validated['name'],
                        'phone' => $validated['phone'] ?? null,
                        'address' => $validated['address'] ?? null,
                    ]
                );

                if ($customer->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } else {
                Customer::create([
                    'name' => $validated['name'],
                    'email' => null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]);
                $created++;
            }
        }

        fclose($handle);

        $redirect = redirect()->route('customers.index');

        if ($created > 0 || $updated > 0) {
            $messages = [];

            if ($created > 0) {
                $messages[] = "{$created} customer ditambahkan";
            }

            if ($updated > 0) {
                $messages[] = "{$updated} customer diperbarui";
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
                'telepon', 'telp', 'no_hp', 'no_telp' => 'phone',
                'alamat' => 'address',
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
