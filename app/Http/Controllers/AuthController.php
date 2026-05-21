<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Customer;

class AuthController extends Controller
{
    // ADMIN LOGIN
    public function adminLogin(Request $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Session::put('user', $admin);
            Session::put('role', 'admin');

            return redirect('/admin');
        }

        return back()->with('error', 'Login admin gagal');
    }

    // CUSTOMER (GUEST)
    public function guestLogin(Request $request)
    {
        $request->validate([
            'nama'    => 'required',
            'no_meja' => 'required'
        ]);

        $customer = Customer::create([
            'nama'    => $request->nama,
            'no_meja' => $request->no_meja
        ]);

        Session::put('user', $customer);
        Session::put('role', 'customer');
        Session::put('customer_name', $request->nama);
        Session::put('no_meja', $request->no_meja);

        return redirect('/dashboard');
    }
}