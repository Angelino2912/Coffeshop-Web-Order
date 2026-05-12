<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Session;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/home', function () {
    return view('home');
});

// CUSTOMER
Route::get('/login', function () {
    return view('auth.login'); // hanya customer
});
Route::post('/guest-login', [AuthController::class, 'guestLogin']);

// admin
Route::get('/admin/login', function () {
    return view('admin.login');
});
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

// CUSTOMER
Route::get('/dashboard', function () {

    $user = Session::get('user');

    return view('customer.dashboard', compact('user'));
});

Route::get('/menu', [CustomerController::class, 'menu']);
Route::post('/cart/add', [CustomerController::class, 'addToCart']);
Route::get('/cart', [CustomerController::class, 'cart']);
Route::post('/cart/update', [CustomerController::class, 'updateCart']);
Route::post('/cart/remove', [CustomerController::class, 'removeFromCart']);
Route::get('/checkout', [CustomerController::class, 'checkout']);
Route::post('/checkout', [CustomerController::class, 'placeOrder']);
Route::get('/order-confirmation', [CustomerController::class, 'orderConfirmation']);
Route::get('/my-orders',[CustomerController::class, 'myOrders']);

// ADMIN
Route::get('/admin', function () {
    if (session('role') != 'admin') {
        return redirect('/login');
    }
    return view('admin.dashboard');
});
Route::get(
    '/admin/orders',
    [AdminController::class, 'orders']
);
Route::put(
    '/admin/orders/{id}/status',
    [AdminController::class, 'updateStatus']
);
