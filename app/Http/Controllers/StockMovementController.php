<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::with('product.category')->latest()->paginate(15);
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        StockMovement::create($validated);

        return redirect()->route('stock-movements.index')->with('success', 'Pergerakan stok berhasil dicatat.');
    }
}
