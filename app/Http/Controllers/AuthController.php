<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Customer;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        // CEK ADMIN
        $admin = Admin::where('email', $email)->first();

        if ($admin && $password == $admin->password) {
            Session::put('user', $admin);
            Session::put('role', 'admin');

            return redirect('/admin');
        }

        // CEK CUSTOMER
        $customer = Customer::where('email', $email)->first();

        if ($customer && Hash::check($password, $customer->password)) {
            Session::put('user', $customer);
            Session::put('role', 'customer');

            return redirect('/dashboard');
        }

        return back()->with('error', 'Login gagal');
    }
}