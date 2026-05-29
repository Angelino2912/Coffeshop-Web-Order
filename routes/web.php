<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\TableController;
use App\Http\Controllers\KasirController;

Route::get('/', function () {
    $mejas = \App\Models\Meja::all();
    return view('auth.login', compact('mejas'));
});

Route::get('/home', function () {
    return redirect('dashboard');
});

// CUSTOMER
Route::get('/login', function () {
    $mejas = \App\Models\Meja::all();
    return view('auth.login', compact('mejas'));
});
Route::post('/guest-login', [AuthController::class, 'guestLogin']);

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

// ADMIN ROUTES (PROTECTED)
Route::get('/admin', function () {
    if (session('role') !== 'admin') {
        return redirect('/admin/login');
    }
    return redirect('/admin/dashboard');
});

Route::middleware('role:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/orders', [AdminController::class, 'orders']);
    Route::put('/orders/{id}/status', [AdminController::class, 'updateStatus']);
    Route::post('/meja/generate-qr', [AdminController::class, 'generateQr']);
    Route::post('/meja/store', [AdminController::class, 'storeMeja']);
    Route::get('/meja/status', [AdminController::class, 'mejaStatus']);
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
    Route::post('/meja/store', [KasirController::class, 'storeMeja'])->name('kasir.meja.store');
    Route::post('/meja/{no_meja}/delete', [KasirController::class, 'destroyMeja'])->name('kasir.meja.destroy');
    Route::post('/orders/{id}/status', [KasirController::class, 'updateStatus'])->name('kasir.orders.status');
    Route::post('/logout', function () {
        session()->forget(['kasir_id', 'role', 'name']);
        return redirect('/admin/login');
    })->name('kasir.logout');
});

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