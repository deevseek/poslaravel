<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        [$cartItems, $subtotal] = $this->cartItems();

        return view('pos.index', [
            'products' => $products,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'paymentMethods' => [
                'cash' => 'Tunai',
                'transfer' => 'Transfer',
                'e-wallet' => 'E-Wallet',
            ],
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $cart = session('cart', []);
        $newQuantity = ($cart[$product->id] ?? 0) + $validated['quantity'];

        if ($newQuantity > $product->stock) {
            return back()->withErrors([
                'quantity' => 'Stok produk tidak mencukupi.',
            ]);
        }

        $cart[$product->id] = $newQuantity;
        session(['cart' => $cart]);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validated['quantity'] > $product->stock) {
            return back()->withErrors([
                'quantity' => 'Stok produk tidak mencukupi.',
            ]);
        }

        $cart = session('cart', []);
        if (! array_key_exists($product->id, $cart)) {
            return back()->withErrors([
                'quantity' => 'Produk tidak ada di keranjang.',
            ]);
        }

        $cart[$product->id] = $validated['quantity'];
        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeFromCart(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->withErrors([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        [$cartItems, $subtotal] = $this->cartItems();

        if ($subtotal <= 0) {
            return back()->withErrors([
                'cart' => 'Tidak ada item yang dapat diproses.',
            ]);
        }

        $validated = $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,e-wallet',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $discount = min($validated['discount'] ?? 0, $subtotal);
        $total = $subtotal - $discount;
        $paidAmount = $validated['paid_amount'];

        if ($paidAmount < $total) {
            return back()->withErrors([
                'paid_amount' => 'Jumlah pembayaran kurang dari total.',
            ]);
        }

        try {
            $transaction = DB::transaction(function () use ($cartItems, $discount, $subtotal, $total, $validated, $paidAmount) {
                $transaction = Transaction::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'payment_method' => $validated['payment_method'],
                    'paid_amount' => $paidAmount,
                    'change_amount' => max($paidAmount - $total, 0),
                ]);

                foreach ($cartItems as $item) {
                    $product = Product::lockForUpdate()->find($item['product']->id);

                    if (! $product || $product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Stok produk tidak mencukupi untuk '.$item['product']->name,
                        ]);
                    }

                    $lineTotal = $product->price * $item['quantity'];

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'total' => $lineTotal,
                        'discount' => 0,
                    ]);

                    $product->decrement('stock', $item['quantity']);
                }

                return $transaction;
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        session()->forget('cart');

        return redirect()->route('pos.receipt', $transaction)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('items.product');

        return view('pos.receipt', [
            'transaction' => $transaction,
        ]);
    }

    protected function cartItems(): array
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return [[], 0];
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product) {
                continue;
            }

            $lineTotal = $product->price * $quantity;

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        return [$items, $subtotal];
    }

    protected function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Transaction::whereDate('created_at', now()->toDateString())->count() + 1;

        return 'INV-'.$date.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
