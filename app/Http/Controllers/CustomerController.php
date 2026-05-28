<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;

class CustomerController extends Controller
{
    public function dashboard()
    {
        return view('customer.dashboard', [
            'name' => session('customer_name') ?? 'Kamu',
            'no_meja' => session('no_meja')
        ]);
    }

    public function menu()
    {
        if (!session('customer_name') || !session('no_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR meja terlebih dahulu');
        }

        $items = Menu::all();

        return view('customer.menu', ['items' => $items]);
    }

    public function addToCart(Request $request)
    {
        if (!session('customer_name') || !session('no_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR meja terlebih dahulu');
        }

        $request->validate([
            'item_id'  => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Menu::find($request->item_id);

        if (!$item) {
            return redirect('/menu')->with('error', 'Menu tidak ditemukan');
        }

        $cart    = Session::get('customer_cart', []);
        $cartKey = $item->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'id'       => $item->id,
                'name'     => $item->name,
                'price'    => $item->price,
                'quantity' => $request->quantity,
            ];
        }

        Session::put('customer_cart', $cart);

        return redirect('/menu')->with('success', 'Menu berhasil ditambahkan ke keranjang');
    }

    public function cart()
    {
        if (!session('customer_name') || !session('no_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR meja terlebih dahulu');
        }

        $cart  = Session::get('customer_cart', []);
        $total = collect($cart)->reduce(fn($total, $item) => $total + ($item['price'] * $item['quantity']), 0);

        return view('customer.cart', ['cart' => $cart, 'total' => $total]);
    }

    public function updateCart(Request $request)
    {
        // Support AJAX (JSON) dan form biasa
        if ($request->isJson()) {
            $data = $request->json()->all();
        } else {
            $data = $request->all();
        }

        $itemId  = $data['item_id'] ?? null;
        $qty     = $data['quantity'] ?? 0;

        if (!$itemId) {
            return $request->isJson()
                ? response()->json(['error' => 'Item tidak ditemukan'], 400)
                : redirect('/cart')->with('error', 'Item tidak ditemukan di keranjang');
        }

        $cart = Session::get('customer_cart', []);

        if (!isset($cart[$itemId])) {
            return $request->isJson()
                ? response()->json(['error' => 'Item tidak ada di keranjang'], 404)
                : redirect('/cart')->with('error', 'Item tidak ditemukan di keranjang');
        }

        if ($qty == 0) {
            unset($cart[$itemId]);
        } else {
            $cart[$itemId]['quantity'] = $qty;
        }

        Session::put('customer_cart', $cart);

        return $request->isJson()
            ? response()->json(['success' => true])
            : redirect('/cart')->with('success', 'Keranjang diperbarui');
    }

    public function removeFromCart(Request $request)
    {
        $request->validate(['item_id' => 'required|integer']);

        $cart = Session::get('customer_cart', []);
        unset($cart[$request->item_id]);
        Session::put('customer_cart', $cart);

        return redirect('/cart')->with('success', 'Item berhasil dihapus dari keranjang');
    }

    public function checkout()
    {
        if (!session('customer_name') || !session('no_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR meja terlebih dahulu');
        }

        $cart = Session::get('customer_cart', []);

        if (empty($cart)) {
            return redirect('/menu')->with('error', 'Keranjang kosong. Silakan pilih menu terlebih dahulu.');
        }

        $total = collect($cart)->reduce(fn($total, $item) => $total + ($item['price'] * $item['quantity']), 0);

        return view('customer.checkout', [
            'cart'         => $cart,
            'total'        => $total,
            'customerName' => session('customer_name'),
            'noMeja'       => session('no_meja'),
        ]);
    }

    public function placeOrder(Request $request)
    {
        if (!session('customer_name') || !session('no_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR meja terlebih dahulu');
        }

        $request->validate(['note' => 'nullable|string|max:500']);

        $cart = Session::get('customer_cart', []);

        if (empty($cart)) {
            return redirect('/menu')->with('error', 'Keranjang kosong.');
        }

        $total = collect($cart)->reduce(fn($total, $item) => $total + ($item['price'] * $item['quantity']), 0);

        $order = Order::create([
            'customer_name' => session('customer_name'),
            'table_number'  => session('no_meja'),
            'note'          => $request->note,
            'total'         => $total,
            'status'        => 'pending',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id'  => $item['id'],
                'quantity' => $item['quantity'],
                'price'    => $item['price'],
            ]);
        }

        Session::forget('customer_cart');
        Session::put('last_order_id', $order->id);

        return redirect('/order-confirmation')->with('success', 'Pesanan berhasil dibuat');
    }

    public function orderConfirmation()
    {
        $orderId = Session::get('last_order_id');

        if (!$orderId) {
            return redirect('/menu')->with('error', 'Tidak ada pesanan yang dapat ditampilkan.');
        }

        $order = Order::with(['items.menu', 'review'])->find($orderId);

        if (!$order) {
            return redirect('/menu')->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('customer.order', ['order' => $order]);
    }

    public function orderStatus()
    {
        $orderId = Session::get('last_order_id');

        if (!$orderId) {
            return response()->json(['success' => false, 'message' => 'Tidak ada pesanan aktif.'], 404);
        }

        $order = Order::with('review')->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        return response()->json([
            'success'      => true,
            'status'       => $order->status,
            'status_label' => $this->orderStatusLabel($order->status),
            'can_review'   => $order->status === 'completed' && !$order->review,
            'reviewed'     => (bool) $order->review,
        ]);
    }

    public function storeReview(Request $request)
    {
        $orderId = Session::get('last_order_id');

        if (!$orderId) {
            return response()->json(['success' => false, 'message' => 'Tidak ada pesanan aktif.'], 404);
        }

        $order = Order::with('review')->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Review bisa dikirim setelah pesanan diantarkan.'], 422);
        }

        if ($order->review) {
            return response()->json(['success' => false, 'message' => 'Review untuk pesanan ini sudah dikirim.'], 422);
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'order_id'      => $order->id,
            'customer_name' => $order->customer_name,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih, review kamu sudah terkirim.',
        ]);
    }

    public function myOrders()
    {
        if (!session('customer_name')) {
            return redirect('/')->with('error', 'Silakan scan QR meja terlebih dahulu');
        }

        $orders = Order::with('review')->where('customer_name', session('customer_name'))->latest()->get();

        return view('customer.myorders', compact('orders'));
    }

    public function myOrdersStatus()
    {
        if (!session('customer_name')) {
            return response()->json(['success' => false, 'message' => 'Sesi customer tidak ditemukan.'], 401);
        }

        $orders = Order::with('review')
            ->where('customer_name', session('customer_name'))
            ->latest()
            ->get()
            ->map(fn($order) => [
                'id'           => $order->id,
                'status'       => $order->status,
                'status_label' => $this->orderStatusLabel($order->status),
                'reviewed'     => (bool) $order->review,
            ]);

        return response()->json(['success' => true, 'orders' => $orders]);
    }

    private function orderStatusLabel(string $status): string
    {
        return match ($status) {
            'pending'   => 'Menunggu konfirmasi',
            'confirmed' => 'Pesanan sedang diproses',
            'completed' => 'Pesanan telah diantarkan',
            default     => ucfirst($status),
        };
    }
}
