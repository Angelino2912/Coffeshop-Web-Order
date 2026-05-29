<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tampilkan form buat order baru
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Simpan order baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number'  => 'required|string|max:50',
            'note'          => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.name'  => 'required|string',
            'items.*.price' => 'required|integer|min:0',
            'items.*.qty'   => 'required|integer|min:1',
        ]);

        // Hitung total
        $total = collect($request->items)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        // Buat order
        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_number'  => $request->table_number,
            'note'          => $request->note,
            'total'         => $total,
            'status'        => 'pending',
        ]);

        // Simpan items (jika ada tabel order_items)
        foreach ($request->items as $item) {
            $order->items()->create([
                'name'     => $item['name'],
                'price'    => $item['price'],
                'quantity' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order berhasil dibuat!');
    }

    /**
     * Tampilkan detail order
     */
    public function show(Order $order)
    {
        $order->load('items');
        return view('orders.show', compact('order'));
    }

    /**
     * Tampilkan semua order (untuk kasir/admin)
     */
    public function index()
    {
        $orders = Order::latest()->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Update status order
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()
            ->with('success', 'Status order berhasil diupdate!');
    }
}