<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Customer;

class AuthController extends Controller
{
    // ADMIN & KASIR LOGIN
public function adminLogin(Request $request)
{
    $request->validate([
        'name'     => 'required',
        'password' => 'required',
    ]);

    $admin = \App\Models\Admin::where('name', $request->name)->first();

   if ($admin && $admin->password === $request->password) {
        if ($admin->role === 'kasir') {
            session([
                'kasir_id' => $admin->id,
                'role'     => 'kasir',
                'name'     => $admin->name,
            ]);
            return redirect('/kasir/dashboard');
        }

        if ($admin->role === 'admin') {
            session([
                'admin_id' => $admin->id,
                'role'     => 'admin',
                'name'     => $admin->name,
            ]);
            return redirect('/admin/dashboard');
        }
    }

    return back()->withErrors(['name' => 'Nama atau password salah']);
}
    // CUSTOMER (GUEST) — tidak diubah
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