<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Meja;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class KasirController extends Controller
{
    public function dashboard()
    {
        $mejas = Meja::all();

        $orders = Order::with('items.menu')
                       ->orderBy('id', 'desc')
                       ->get();

        // Data chart mingguan
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date           = now()->subDays($i);
            $weeklyLabels[] = $date->locale('id')->isoFormat('ddd');
            $weeklyData[]   = (int) Order::whereDate('created_at', $date)->sum('total');
        }

        // Data chart kategori
        $categoryData = [
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->where('category', 'makanan'))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->where('category', 'minuman'))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->where('category', 'snack'))->sum('quantity'),
        ];

        return view('kasir.dashboard', compact(
            'mejas',
            'orders',
            'weeklyLabels',
            'weeklyData',
            'categoryData',
        ));
    }

    public function mejaStatus()
    {
        $mejas = Meja::all()->map(function ($meja) {
            $activeOrder = Order::where('table_number', $meja->no_meja)
                ->whereIn('status', ['pending', 'confirmed'])
                ->latest()
                ->first();

            if ($activeOrder && $activeOrder->status === 'confirmed') {
                $status       = 'aktif';
                $customerName = $activeOrder->customer_name;
            } elseif ($activeOrder && $activeOrder->status === 'pending') {
                $status       = 'pending';
                $customerName = $activeOrder->customer_name;
            } else {
                $status       = 'kosong';
                $customerName = null;
            }

            return [
                'no_meja'       => $meja->no_meja,
                'qr_uuid'       => $meja->qr_uuid,
                'status'        => $status,
                'customer_name' => $customerName,
            ];
        });

        return response()->json($mejas);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed',
        ]);

        $order         = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success'      => true,
                'status'       => $order->status,
                'status_label' => match ($order->status) {
                    'pending'   => 'Pending',
                    'confirmed' => 'Diproses',
                    'completed' => 'Selesai',
                    default     => ucfirst($order->status),
                },
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diupdate');
    }

    public function storeMeja(Request $request)
    {
        $request->validate([
            'no_meja' => 'required|unique:meja,no_meja'
        ]);

        $meja          = new Meja();
        $meja->no_meja = $request->no_meja;
        $meja->qr_uuid = Str::uuid();
        $meja->save();

        $url     = url('/table/' . $meja->qr_uuid);
        $qrImage = QrCode::format('svg')->size(300)->generate($url);
        Storage::disk('public')->put('qr/meja_' . $meja->no_meja . '.svg', $qrImage);

        return back()->with('success', 'Meja berhasil ditambahkan & QR otomatis digenerate');
    }

    public function destroyMeja($no_meja) // ← fix: pakai no_meja bukan id
    {
        $meja = Meja::where('no_meja', $no_meja)->firstOrFail();
        $meja->delete();

        return response()->json(['success' => true]);
    }
}