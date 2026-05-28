<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\TableController;

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
Route::post('/guest-login', [AuthController::class, 'guestLogin']);

// ADMIN AUTH
Route::get('/admin/login', function () {
    return view('admin.login');
});
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// CUSTOMER PAGES
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
Route::get('/order-confirmation/status', [CustomerController::class, 'orderStatus']);
Route::post('/order-confirmation/review', [CustomerController::class, 'storeReview']);
Route::get('/my-orders', [CustomerController::class, 'myOrders']);
Route::get('/my-orders/status', [CustomerController::class, 'myOrdersStatus']);

// TABLE (QR Scan)
Route::get('/table/end', [TableController::class, 'endSession']);
Route::post('/table/confirm', [TableController::class, 'confirm']);
Route::get('/table/{qr}', [TableController::class, 'scan']);

// ADMIN
Route::get('/admin', function () {
    if (session('role') !== 'admin') {
        return redirect('/admin/login'); // ← fix: arahkan ke admin login
    }
    return redirect('/admin/dashboard');
});
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/orders', [AdminController::class, 'orders']);
Route::put('/admin/orders/{id}/status', [AdminController::class, 'updateStatus']);
Route::post('/admin/meja/generate-qr', [AdminController::class, 'generateQr']);
Route::post('/admin/meja/store', [AdminController::class, 'storeMeja']);
Route::get('/admin/meja/status', [AdminController::class, 'mejaStatus']);
Route::get('/admin/manajemen-menu', [AdminController::class, 'manajemenMenu']);
Route::post('/admin/manajemen-menu', [AdminController::class, 'storeMenu']);
Route::put('/admin/manajemen-menu/{id}', [AdminController::class, 'updateMenu']);
Route::delete('/admin/manajemen-menu/{id}', [AdminController::class, 'destroyMenu']);
Route::get('/admin/reviews', [AdminController::class, 'reviews']);
Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');

// KASIR
Route::get('/kasir/dashboard', [KasirController::class, 'dashboard']);
Route::post('/kasir/meja/store', [KasirController::class, 'storeMeja']);
Route::post('/kasir/orders/{id}/status', [KasirController::class, 'updateStatus']);

// TEMPORARY UTILITY ROUTES (hapus setelah dipakai)
Route::get('/admin/fix-qr', function () {
    \App\Models\Meja::all()->each(function ($meja) {
        if (!$meja->qr_uuid) {
            $meja->qr_uuid = \Illuminate\Support\Str::uuid();
            $meja->save();
        }
    });
    return 'Done: semua meja sudah punya UUID';
});

Route::get('/admin/force-generate-qr', function () {
    $mejas = \App\Models\Meja::all();
    foreach ($mejas as $meja) {
        $url = url('/table/' . $meja->qr_uuid);
        $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($url);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qr/meja_' . $meja->no_meja . '.svg', $qrImage);
    }
    return 'QR semua meja berhasil digenerate';
});

Route::post('/admin/meja/{no_meja}/delete', [TableController::class, 'destroy']);
