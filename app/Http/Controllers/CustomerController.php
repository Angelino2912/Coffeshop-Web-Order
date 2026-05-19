<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;

class CustomerController extends Controller
{
    public function dashboard()
    {
        return view('customer.dashboard', [
            'name' => session('user')->name ?? 'Kamu',
        ]);
    }

    public function menu()
    {
        $items = Menu::all();
        return view('customer.menu', [
            'items' => $items,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Menu::find($request->item_id);

        if (! $item) {
            return redirect('/menu')->with('error', 'Menu tidak ditemukan');
        }

        $cart = Session::get('customer_cart', []);
        $cartKey = $item->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $request->quantity,
            ];
        }

        Session::put('customer_cart', $cart);

        return redirect('/menu')->with('success', 'Menu berhasil ditambahkan ke keranjang');
    }

    public function cart()
    {
        $cart = Session::get('customer_cart', []);
        $total = collect($cart)->reduce(fn ($total, $item) => $total + ($item['price'] * $item['quantity']), 0);

        return view('customer.cart', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = Session::get('customer_cart', []);
        $itemId = $request->item_id;

        if (! isset($cart[$itemId])) {
            return redirect('/cart')->with('error', 'Item tidak ditemukan di keranjang');
        }

        if ($request->quantity === 0) {
            unset($cart[$itemId]);
        } else {
            $cart[$itemId]['quantity'] = $request->quantity;
        }

        Session::put('customer_cart', $cart);

        return redirect('/cart')->with('success', 'Keranjang diperbarui');
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $cart = Session::get('customer_cart', []);
        unset($cart[$request->item_id]);
        Session::put('customer_cart', $cart);

        return redirect('/cart')->with('success', 'Item berhasil dihapus dari keranjang');
    }

    public function checkout()
    {
        $cart = Session::get('customer_cart', []);

        if (empty($cart)) {
            return redirect('/menu')->with('error', 'Keranjang kosong. Silakan pilih menu terlebih dahulu.');
        }

        $total = collect($cart)->reduce(fn ($total, $item) => $total + ($item['price'] * $item['quantity']), 0);
        $customerName = session('customer_name');
        $noMeja = session('no_meja');

        if (!$customerName || !$noMeja) {
            return redirect('/login')->with('error', 'Session customer tidak ditemukan');
        }

        return view('customer.checkout', [
        'cart' => $cart,
        'total' => $total,
        'customerName' => $customerName,
        'noMeja' => $noMeja,
    ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $customerName = session('customer_name');
        $noMeja = session('no_meja');

        if (!$customerName || !$noMeja) {
            return redirect('/login')->with(
                'error',
                'Session customer tidak ditemukan'
            );
        }

        $cart = Session::get('customer_cart', []);

        if (empty($cart)) {
            return redirect('/menu')->with(
                'error',
                'Keranjang kosong.'
            );
        }

        $total = collect($cart)->reduce(
            fn ($total, $item)
                => $total + ($item['price'] * $item['quantity']),
            0
        );

        $order = Order::create([
            'customer_name' => $customerName,
            'table_number' => $noMeja,
            'note' => $request->note,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($cart as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        Session::forget('customer_cart');

        Session::put('last_order_id', $order->id);

        return redirect('/order-confirmation')
            ->with('success', 'Pesanan berhasil dibuat');
    }
    public function orderConfirmation()
    {
        $orderId = Session::get('last_order_id');

        if (! $orderId) {
            return redirect('/menu')->with('error', 'Tidak ada pesanan yang dapat ditampilkan.');
        }

        $order = Order::with('items.menu')->find($orderId);

        if (! $order) {
            return redirect('/menu')->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('customer.order', [
            'order' => $order,
        ]);
    }
    public function myOrders()
    {
        $customerName = session('customer_name');

        if (!$customerName) {
            return redirect('/login')->with(
                'error',
                'Session customer tidak ditemukan'
            );
        }

        $orders = Order::where(
            'customer_name',
            $customerName
        )
        ->latest()
        ->get();

        return view(
            'customer.myorders',
            compact('orders`')
        );
    }
}
