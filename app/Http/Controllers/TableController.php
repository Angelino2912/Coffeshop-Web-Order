<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meja;
use App\Models\TableSession;
use Illuminate\Support\Str;
use Session;

class TableController extends Controller
{
    /**
     * STEP 1: Scan QR
     * URL: /table/{qr_uuid}
     */
    public function scan($qr)
    {
        // cari meja berdasarkan UUID QR
        $meja = Meja::where('qr_uuid', $qr)->first();

        if (!$meja) {
            return abort(404, 'QR tidak valid');
        }

        // cek apakah meja sedang dipakai
        $activeSession = TableSession::where('meja_id', $meja->id)
            ->where('status', 'active')
            ->first();

        if ($activeSession) {
            return redirect('/login')->with('error', 'Meja sedang digunakan');
        }

        // simpan sementara ke session (untuk form input nama)
        session([
            'meja_id_temp' => $meja->id,
            'no_meja_temp' => $meja->no_meja
        ]);

        // tampilkan form input nama
        return view('auth.login', compact('meja'));
    }

    /**
     * STEP 2: Confirm nama + LOCK meja
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255'
        ]);

        $mejaId = session('meja_id_temp');

        if (!$mejaId) {
            return redirect('/home')->with('error', 'Session meja tidak ditemukan');
        }

        $meja = Meja::find($mejaId);

        if (!$meja) {
            return redirect('/home')->with('error', 'Meja tidak valid');
        }

        // LOCK MEJA (create session)
        $session = TableSession::create([
            'meja_id' => $meja->id,
            'session_uuid' => Str::uuid(),
            'customer_name' => $request->customer_name,
            'status' => 'active',
            'started_at' => now()
        ]);

        // simpan ke session Laravel
        session([
            'meja_id' => $meja->id,
            'no_meja' => $meja->no_meja,
            'customer_name' => $request->customer_name,
            'session_uuid' => $session->session_uuid
        ]);

        // hapus temp session
        session()->forget(['meja_id_temp', 'no_meja_temp']);

        // langsung ke menu
        return redirect('/dashboard')->with('success', 'Selamat datang di meja ' . $meja->no_meja);
    }

    /**
     * OPTIONAL: end session (checkout meja)
     */
    public function endSession()
    {
        $sessionUuid = session('session_uuid');

        if ($sessionUuid) {
            TableSession::where('session_uuid', $sessionUuid)
                ->update([
                    'status' => 'finished',
                    'ended_at' => now()
                ]);
        }

        session()->forget([
            'meja_id',
            'no_meja',
            'customer_name',
            'session_uuid'
        ]);

        return redirect('/dashboard')->with('success', 'Terima kasih sudah datang');
    }
}