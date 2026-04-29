<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
});

// AUTH
Route::get('/login', function () {
    return view('auth.login');
});
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

// CUSTOMER
Route::get('/dashboard', function () {
    return view('customer.dashboard');
});

Route::get('/menu', function () {
    return view('customer.menu');
});

Route::get('/cart', function () {
    return view('customer.cart');
});

// ADMIN
Route::get('/admin', function () {
    if (session('role') != 'admin') {
        return redirect('/login');
    }
    return view('admin.dashboard');
});
