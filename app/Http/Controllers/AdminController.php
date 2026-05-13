<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Meja;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
class AdminController extends Controller
{
    public function orders()
    {
        $orders = Order::latest()->get();

        return view('admin.orders', [
            'orders' => $orders
        ]);
    }
    public function dashboard()
    {
        $mejas = Meja::all();
        return view('admin.dashboard', compact('mejas'));
    }


    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->status = $request->status;

        $order->save();

        return back()->with(
            'success',
            'Status pesanan berhasil diupdate'
        );
    }

    public function generateQr()
    {
        $mejas = Meja::all();

        foreach ($mejas as $meja) {

            // kalau belum ada UUID
            if (!$meja->qr_uuid) {
                $meja->qr_uuid = Str::uuid();
                $meja->save();
            }

            // link QR
            $url = url('/table/' . $meja->qr_uuid);

            // generate QR image
            $qrImage = QrCode::format('svg')
                ->size(300)
                ->generate($url);

            $fileName = 'qr/meja_' . $meja->no_meja . '.svg';

            // simpan file
            $fileName = 'qr/meja_' . $meja->no_meja . '.svg';

            Storage::disk('public')->put($fileName, $qrImage);
        }

        return redirect()->back()->with('success', 'QR semua meja berhasil digenerate');
    }
    public function storeMeja(Request $request)
    {
        $request->validate([
            'no_meja' => 'required|unique:meja,no_meja'
        ]);

        Meja::create([
            'no_meja' => $request->no_meja
        ]);

        return back()->with('success', 'Meja berhasil ditambahkan');
    }

}