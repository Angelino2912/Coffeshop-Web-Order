<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
class AdminController extends Controller
{
    public function orders()
    {
        $orders = Order::latest()->get();

        return view('admin.orders', [
            'orders' => $orders
        ]);
    }
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->status = $request->status;

        $order->save();

        return back()->with(
            'success',
            'Status pesanan berhasil diupdate'
        );
    }

}