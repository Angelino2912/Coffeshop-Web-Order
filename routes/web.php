<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

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
Route::get('/dashboard', [CustomerController::class, 'dashboard']);
Route::get('/menu', [CustomerController::class, 'menu']);
Route::post('/cart/add', [CustomerController::class, 'addToCart']);
Route::get('/cart', [CustomerController::class, 'cart']);
Route::post('/cart/update', [CustomerController::class, 'updateCart']);
Route::post('/cart/remove', [CustomerController::class, 'removeFromCart']);
Route::get('/checkout', [CustomerController::class, 'checkout']);
Route::post('/checkout', [CustomerController::class, 'placeOrder']);
Route::get('/order-confirmation', [CustomerController::class, 'orderConfirmation']);

// ADMIN
Route::get('/admin', function () {
    if (session('role') != 'admin') {
        return redirect('/login');
    }
    return view('admin.dashboard');
});
