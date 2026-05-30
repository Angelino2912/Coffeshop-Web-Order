<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\TableController;
use App\Http\Controllers\KasirController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', function () {
    return redirect('dashboard');
});

// ─── CUSTOMER AUTH ────────────────────────────────────────────────────────────
Route::get('/login', function () {
    return view('auth.login');
});
Route::post('/guest-login', [AuthController::class, 'guestLogin']);

// ─── AUTHNYA ───────────────────────────────────────────────────────────────
Route::get('/login-karyawan', function () {
    return view('auth.login-karyawan');
})->name('login.karyawan');

Route::post('/login-karyawan', [AuthController::class, 'adminLogin']);

// ─── CUSTOMER PAGES ───────────────────────────────────────────────────────────
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

// ─── TABLE (QR Scan) ──────────────────────────────────────────────────────────
Route::get('/table/end', [TableController::class, 'endSession']);
Route::post('/table/confirm', [TableController::class, 'confirm']);
Route::get('/table/{qr}', [TableController::class, 'scan']);

// ─── ADMIN ────────────────────────────────────────────────────────────────────
Route::get('/admin', function () {
    if (session('role') !== 'admin') {
        return redirect('/admin/login');
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
Route::post('/admin/meja/{no_meja}/delete', [TableController::class, 'destroy']);
Route::post('/admin/logout', function () {
    session()->forget(['admin_id', 'role', 'name']);
    return redirect('/admin/login');
})->name('admin.logout');

// ─── KASIR ────────────────────────────────────────────────────────────────────
Route::prefix('kasir')->name('kasir.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

    // Meja
    Route::get('/meja', [KasirController::class, 'mejaIndex'])->name('meja');
    Route::get('/meja/status', [KasirController::class, 'mejaStatus'])->name('meja.status');
    Route::post('/meja/store', [KasirController::class, 'storeMeja'])->name('meja.store');
    Route::post('/meja/{no_meja}/delete', [KasirController::class, 'destroyMeja'])->name('meja.destroy');

    // Orders
    Route::get('/orders', [KasirController::class, 'ordersIndex'])->name('orders');
    Route::post('/orders', [KasirController::class, 'storeOrder'])->name('orders.store');
    Route::post('/orders/{id}/status', [KasirController::class, 'updateStatus'])->name('orders.status');
    Route::delete('/orders/{id}', [KasirController::class, 'destroy'])->name('orders.destroy');

    // Pembayaran
    Route::get('/pembayaran/{id}', [KasirController::class, 'pembayaran'])->name('pembayaran');
    Route::get('/payment/{id}', [KasirController::class, 'pembayaran'])->name('payment.show');
    Route::post('/pembayaran/{id}/process', [KasirController::class, 'processPayment'])->name('payment.process');
});

Route::post('/kasir/logout', function () {
    session()->forget(['kasir_id', 'role', 'name']);
    return redirect('/login-karyawan');
})->name('kasir.logout');

// ─── TEMPORARY UTILITY (hapus setelah dipakai) ───────────────────────────────
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
        $url     = url('/table/' . $meja->qr_uuid);
        $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($url);
        \Illuminate\Support\Facades\Storage::disk('public')->put('qr/meja_' . $meja->no_meja . '.svg', $qrImage);
    }
    return 'QR semua meja berhasil digenerate';
});