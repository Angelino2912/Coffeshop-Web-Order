<?php

use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Route;

Route::prefix('kasir')->middleware(['auth', 'role:kasir'])->group(function () {

    // Dashboard utama kasir (Status Meja + Order Masuk)
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');

    // Status meja (JSON polling untuk update realtime)
    Route::get('/meja-status', [KasirController::class, 'mejaStatus'])->name('kasir.meja.status');

    // Tambah & hapus meja
    Route::post('/meja', [KasirController::class, 'storeMeja'])->name('kasir.meja.store');
    Route::delete('/meja/{id}', [KasirController::class, 'destroyMeja'])->name('kasir.meja.destroy');

    // Update status order (Pending → Diproses → Selesai)
    Route::patch('/orders/{id}/status', [KasirController::class, 'updateStatus'])->name('kasir.orders.status');

});