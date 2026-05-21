<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meja;
use App\Models\TableSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function scan($qr)
    {
        $meja = Meja::where('qr_uuid', $qr)->first();

        if (!$meja) {
            abort(404, 'QR tidak valid');
        }

        // cari session aktif
        $activeSession = TableSession::where('meja_id', $meja->id)
            ->where('status', 'active')
            ->first();

        // jika ada session aktif
        if ($activeSession) {

            // kalau session milik user yang sama
            if (session('session_uuid') == $activeSession->session_uuid) {
                return redirect('/dashboard');
            }

            // AUTO EXPIRE SESSION LAMA (lebih dari 3 jam)
            if ($activeSession->started_at < now()->subHours(3)) {

                $activeSession->update([
                    'status' => 'finished',
                    'ended_at' => now()
                ]);

            } else {

                return abort(403, 'MEJA SEDANG DIGUNAKAN OLEH TAMU LAIN.');
            }
        }

        session([
            'meja_id_temp' => $meja->id,
            'no_meja_temp' => $meja->no_meja,
        ]);

        return view('auth.login', compact('meja'));
    }

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

        try {
            $session = DB::transaction(function () use ($meja, $request) {
                // Lock row agar tidak ada 2 session dibuat bersamaan
                $existing = TableSession::where('meja_id', $meja->id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    throw new \Exception('Meja sudah digunakan oleh tamu lain.');
                }

                return TableSession::create([
                    'meja_id'       => $meja->id,
                    'session_uuid'  => Str::uuid(),
                    'customer_name' => $request->customer_name,
                    'status'        => 'active',
                    'started_at'    => now(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect('/home')->with('error', $e->getMessage());
        }

        session([
            'meja_id'       => $meja->id,
            'no_meja'       => $meja->no_meja,
            'customer_name' => $request->customer_name,
            'session_uuid'  => $session->session_uuid,
        ]);

        session()->forget(['meja_id_temp', 'no_meja_temp']);

        return redirect('/dashboard')->with('success', 'Selamat datang di meja ' . $meja->no_meja);
    }

    public function endSession()
    {
        $sessionUuid = session('session_uuid');

        if (!$sessionUuid) {
            return redirect('/')->with('error', 'Tidak ada sesi aktif.');
        }

        TableSession::where('session_uuid', $sessionUuid)
            ->where('status', 'active')
            ->update([
                'status'   => 'finished',
                'ended_at' => now(),
            ]);

        session()->flush();

        return redirect('/')->with('success', 'Terima kasih sudah datang!');
    }

    public function destroy($no_meja)
    {
        $meja = Meja::where('no_meja', $no_meja)->firstOrFail();

        // Cek ada order aktif tidak
        $aktif = \App\Models\Order::where('table_number', $no_meja)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->exists();

        if ($aktif) {
            return response()->json(['success' => false, 'message' => 'Meja masih aktif.']);
        }

        $meja->delete();

        return response()->json(['success' => true]);
    }

    
}