<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KasirController extends Controller
{
    public function dashboard()
    {
        $mejas = Meja::orderByRaw('CAST(no_meja AS UNSIGNED), no_meja')->get();

        $orders = Order::with('items.menu')
            ->orderBy('id', 'desc')
            ->get();

        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date           = now()->subDays($i);
            $weeklyLabels[] = $date->locale('id')->isoFormat('ddd');
            $weeklyData[]   = (int) Order::whereDate('created_at', $date)->sum('total');
        }

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

    public function generateQr()
    {
        $mejas = Meja::all();

        foreach ($mejas as $meja) {
            if (!$meja->qr_uuid) {
                $meja->qr_uuid = Str::uuid();
                $meja->save();
            }

            $url      = url('/table/' . $meja->qr_uuid);
            $qrImage  = QrCode::format('svg')->size(300)->generate($url);
            $fileName = 'qr/meja_' . $meja->no_meja . '.svg';

            Storage::disk('public')->put($fileName, $qrImage);
        }

        return back()->with('success', 'QR semua meja berhasil digenerate');
    }
}
