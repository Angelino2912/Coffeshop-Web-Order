<?php

namespace App\Http\Controllers;

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

        // jika ada session aktif, pakai session tersebut langsung
        if ($activeSession) {
            session([
                'meja_id'       => $meja->id,
                'no_meja'       => $meja->no_meja,
                'customer_name' => $activeSession->customer_name,
                'session_uuid'  => $activeSession->session_uuid,
            ]);
            return redirect('/menu');
        }

        // Jika tidak ada session aktif, buat session baru secara otomatis
        $customerName = 'Tamu Meja ' . $meja->no_meja;
        
        try {
            $session = DB::transaction(function () use ($meja, $customerName) {
                // Lock row agar tidak ada 2 session dibuat bersamaan
                $existing = TableSession::where('meja_id', $meja->id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                return TableSession::create([
                    'meja_id'       => $meja->id,
                    'session_uuid'  => (string) Str::uuid(),
                    'customer_name' => $customerName,
                    'status'        => 'active',
                    'started_at'    => now(),
                ]);
            });
        } catch (\Exception $e) {
            $session = TableSession::create([
                'meja_id'       => $meja->id,
                'session_uuid'  => (string) Str::uuid(),
                'customer_name' => $customerName,
                'status'        => 'active',
                'started_at'    => now(),
            ]);
        }

        session([
            'meja_id'       => $meja->id,
            'no_meja'       => $meja->no_meja,
            'customer_name' => $session->customer_name,
            'session_uuid'  => $session->session_uuid,
        ]);

        return redirect('/menu')->with('success', 'Selamat datang di meja ' . $meja->no_meja);
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
