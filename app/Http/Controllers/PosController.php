<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Finance;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Warranty;
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
            'customers' => Customer::orderBy('name')->get(),
            'recentTransactions' => Transaction::with('customer')
                ->latest()
                ->limit(10)
                ->get(),
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
            'customer_id' => 'nullable|exists:customers,id',
            'new_customer_name' => 'nullable|string|max:255|required_with:new_customer_email,new_customer_phone,new_customer_address',
            'new_customer_email' => 'nullable|email|max:255|unique:customers,email',
            'new_customer_phone' => 'nullable|string|max:50',
            'new_customer_address' => 'nullable|string|max:255',
        ]);

        $discount = min($validated['discount'] ?? 0, $subtotal);
        $total = $subtotal - $discount;
        $paidAmount = $validated['paid_amount'];

        $customerId = $validated['customer_id'] ?? null;
        if (! $customerId && filled($validated['new_customer_name'] ?? null)) {
            $customer = Customer::create([
                'name' => $validated['new_customer_name'],
                'email' => $validated['new_customer_email'] ?? null,
                'phone' => $validated['new_customer_phone'] ?? null,
                'address' => $validated['new_customer_address'] ?? null,
            ]);
            $customerId = $customer->id;
        }

        if ($paidAmount < $total) {
            return back()->withErrors([
                'paid_amount' => 'Jumlah pembayaran kurang dari total.',
            ]);
        }

        try {
            $transaction = DB::transaction(function () use ($cartItems, $discount, $subtotal, $total, $customerId, $validated, $paidAmount) {
                $totalHpp = 0;

                $transaction = Transaction::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'customer_id' => $customerId,
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
                    $hpp = $product->cost_price ?? 0;
                    $subtotalHpp = $hpp * $item['quantity'];

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'hpp' => $hpp,
                        'subtotal_hpp' => $subtotalHpp,
                        'total' => $lineTotal,
                        'discount' => 0,
                    ]);

                    $totalHpp += $subtotalHpp;

                    $this->createProductWarranty($transaction, $product, $item['quantity']);

                    $product->decrement('stock', $item['quantity']);

                    StockMovement::withoutEvents(function () use ($product, $transaction, $item) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type' => StockMovement::TYPE_OUT,
                            'source' => 'pos',
                            'reference' => $transaction->id,
                            'quantity' => $item['quantity'],
                            'note' => 'Penjualan POS - '.$transaction->invoice_number,
                        ]);
                    });
                }

                Finance::create([
                    'type' => 'income',
                    'category' => 'Penjualan',
                    'nominal' => $total,
                    'note' => 'Pembayaran POS - '.$transaction->invoice_number,
                    'recorded_at' => $transaction->created_at->toDateString(),
                    'source' => 'pos',
                    'reference_id' => $transaction->id,
                    'reference_type' => 'pos',
                    'created_by' => auth()->id(),
                ]);

                Finance::create([
                    'type' => 'expense',
                    'category' => 'HPP',
                    'nominal' => $totalHpp,
                    'note' => 'HPP POS - '.$transaction->invoice_number,
                    'recorded_at' => $transaction->created_at->toDateString(),
                    'source' => 'pos',
                    'reference_id' => $transaction->id,
                    'reference_type' => 'pos',
                    'created_by' => auth()->id(),
                ]);

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
        $transaction->load(['items.product', 'customer']);

        $store = [
            'name' => Setting::getValue(Setting::STORE_NAME, config('app.name')),
            'address' => Setting::getValue(Setting::STORE_ADDRESS),
            'phone' => Setting::getValue(Setting::STORE_PHONE),
            'hours' => Setting::getValue(Setting::STORE_HOURS),
            'logo' => Setting::getValue(Setting::STORE_LOGO_PATH),
        ];

        return view('pos.receipt', [
            'transaction' => $transaction,
            'store' => $store,
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

        $prefix = Setting::getValue(Setting::TRANSACTION_PREFIX, 'INV');
        $padding = (int) Setting::getValue(Setting::TRANSACTION_PADDING, 4);

        return $prefix . '-' . $date . '-' . str_pad((string) $count, $padding, '0', STR_PAD_LEFT);
    }

    protected function createProductWarranty(Transaction $transaction, Product $product, int $quantity): void
    {
        if (($product->warranty_days ?? 0) <= 0) {
            return;
        }

        Warranty::create([
            'type' => Warranty::TYPE_PRODUCT,
            'reference_id' => $transaction->id,
            'customer_id' => $transaction->customer_id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($product->warranty_days)->toDateString(),
            'description' => 'Garansi produk ' . $product->name . ' (Qty: ' . $quantity . ')',
            'status' => Warranty::STATUS_ACTIVE,
        ]);
    }
}
