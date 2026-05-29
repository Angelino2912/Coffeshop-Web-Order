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
        $login = $request->input('email') ?? $request->input('name') ?? $request->input('username');

        $request->validate([
            'password' => 'required',
        ]);

        if (empty($login)) {
            return back()->withErrors(['email' => 'Email atau nama wajib diisi'])->withInput();
        }

        // Determine if the input is email or name
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        $admin = $isEmail
            ? \App\Models\Admin::where('email', $login)->first()
            : \App\Models\Admin::where('name', $login)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            if ($admin->role === 'kasir') {
                session([
                    'kasir_id' => $admin->id,
                    'role'     => 'kasir',
                    'name'     => $admin->name,
                ]);
                return redirect('/kasir/dashboard');
            }

            // Default: admin
            session([
                'admin_id' => $admin->id,
                'role'     => 'admin',
                'name'     => $admin->name,
            ]);
            return redirect('/admin/dashboard');
        }

        return back()->withErrors(['email' => 'Email/nama atau password salah'])->withInput();
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