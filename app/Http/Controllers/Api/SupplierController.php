<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // GET /api/v1/suppliers
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }

        $perPage = $request->per_page ?? 15;
        $suppliers = $query->paginate($perPage);

        return response()->json([
            'data' => $suppliers->items(),
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem(),
            ],
            'links' => [
                'first' => $suppliers->url(1),
                'last' => $suppliers->url($suppliers->lastPage()),
                'prev' => $suppliers->previousPageUrl(),
                'next' => $suppliers->nextPageUrl(),
            ],
        ]);
    }

    // GET /api/v1/suppliers/{id}
    public function show(Supplier $supplier)
    {
        return response()->json(['data' => $supplier]);
    }

    // POST /api/v1/suppliers
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json(['data' => $supplier], 201);
    }

    // PATCH /api/v1/suppliers/{id}
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_person' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => 'sometimes|nullable|string|max:50',
            'address' => 'sometimes|nullable|string',
        ]);

        $supplier->update($validated);

        return response()->json(['data' => $supplier]);
    }

    // DELETE /api/v1/suppliers/{id}
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
