<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\KasirController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', function () {
    return redirect('dashboard');
});

// CUSTOMER
Route::get('/login', function () {
    return view('auth.login');
});

// ADMIN AUTH
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'adminLogin']);

Route::get('/login-karyawan', function () {
    return redirect('/admin/login');
})->name('login.karyawan');

Route::post('/login-karyawan', [AuthController::class, 'adminLogin']);

// CUSTOMER PAGES
Route::get('/dashboard', function () {
    return redirect('/menu');
});
Route::get('/menu', [CustomerController::class, 'menu']);
Route::post('/cart/add', [CustomerController::class, 'addToCart']);
Route::get('/cart', [CustomerController::class, 'cart']);
Route::post('/cart/update', [CustomerController::class, 'updateCart']);
Route::post('/cart/remove', [CustomerController::class, 'removeFromCart']);
Route::get('/checkout', [CustomerController::class, 'checkout']);
Route::post('/checkout', [CustomerController::class, 'placeOrder']);
Route::get('/order-confirmation', [CustomerController::class, 'orderConfirmation']);
Route::get('/order-confirmation/status', [CustomerController::class, 'orderStatus']);
Route::post('/order-confirmation/review', [CustomerController::class, 'storeReview']);
Route::get('/my-orders', [CustomerController::class, 'myOrders']);
Route::get('/my-orders/status', [CustomerController::class, 'myOrdersStatus']);

// TABLE (QR Scan)
Route::get('/table/end', [TableController::class, 'endSession']);
Route::get('/table/{qr}', [TableController::class, 'scan']);

// ADMIN ROUTES (PROTECTED)
Route::get('/admin', function () {
    if (session('role') !== 'admin') {
        return redirect('/admin/login');
    }
    return redirect('/admin/dashboard');
});

Route::middleware('role:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/manajemen-meja', [AdminController::class, 'manajemenMeja']);
    Route::post('/meja/store', [AdminController::class, 'storeMeja']);
    Route::get('/manajemen-menu', [AdminController::class, 'manajemenMenu']);
    Route::post('/manajemen-menu', [AdminController::class, 'storeMenu']);
    Route::put('/manajemen-menu/{id}', [AdminController::class, 'updateMenu']);
    Route::delete('/manajemen-menu/{id}', [AdminController::class, 'destroyMenu']);
    Route::get('/reviews', [AdminController::class, 'reviews']);
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::post('/logout', function () {
        session()->forget(['admin_id', 'role', 'name']);
        return redirect('/admin/login');
    })->name('admin.logout');
    Route::post('/meja/{no_meja}/delete', [TableController::class, 'destroy']);
});

// KASIR ROUTES (PROTECTED)
Route::middleware('role:kasir')->prefix('kasir')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    Route::get('/meja/status', [KasirController::class, 'mejaStatus'])->name('kasir.meja.status');
    Route::post('/meja/generate-qr', [KasirController::class, 'generateQr'])->name('kasir.meja.generate-qr');
    Route::post('/orders/{id}/status', [KasirController::class, 'updateStatus'])->name('kasir.orders.status');
    Route::post('/logout', function () {
        session()->forget(['kasir_id', 'role', 'name']);
        return redirect('/admin/login');
    })->name('kasir.logout');
});
