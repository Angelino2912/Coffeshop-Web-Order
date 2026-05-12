<?php

namespace App\Http\Controllers;

use App\Models\Order;

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
}