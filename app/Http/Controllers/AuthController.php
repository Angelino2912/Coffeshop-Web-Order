<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        $login = $request->input('email') ?? $request->input('name') ?? $request->input('username');

        $request->validate([
            'password' => 'required',
        ]);

        if (empty($login)) {
            return back()->withErrors(['email' => 'Email atau nama wajib diisi'])->withInput();
        }

        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        $admin = $isEmail
            ? Admin::where('email', $login)->first()
            : Admin::where('name', $login)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            if ($admin->role === 'kasir') {
                session([
                    'kasir_id' => $admin->id,
                    'role'     => 'kasir',
                    'name'     => $admin->name,
                ]);

                return redirect('/kasir/dashboard');
            }

            session([
                'admin_id' => $admin->id,
                'role'     => 'admin',
                'name'     => $admin->name,
            ]);

            return redirect('/admin/dashboard');
        }

        return back()->withErrors(['email' => 'Email/nama atau password salah'])->withInput();
    }
}
