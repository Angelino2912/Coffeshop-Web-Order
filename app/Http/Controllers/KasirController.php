<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class KasirController extends Controller
{
    public function dashboard()
    {
        $mejas = Meja::all();

        // Hitung status tiap meja dari tabel orders
        $mejaStatuses = [];
        foreach ($mejas as $meja) {
            $activeOrder = Order::where('table_number', $meja->no_meja)
                ->whereIn('status', ['pending', 'confirmed'])
                ->latest()->first();

            if ($activeOrder?->status === 'confirmed') {
                $mejaStatuses[$meja->no_meja] = 'aktif';
            } elseif ($activeOrder?->status === 'pending') {
                $mejaStatuses[$meja->no_meja] = 'pending';
            } else {
                $mejaStatuses[$meja->no_meja] = 'kosong';
            }
        }

        $orders = Order::with('items.menu')
                       ->orderBy('id', 'desc')
                       ->get();

        // Menu untuk panel kasir (grouped by category string)
        $menus = Menu::orderBy('category')->orderBy('name')->get();

        // Kategori unik dari kolom string 'category' di tabel menus
        $categories = $menus->groupBy('category')->map(function ($items, $name) {
            return (object) [
                'id'          => $name,           // pakai nama string sebagai id filter
                'name'        => ucfirst($name),
                'menus_count' => $items->count(),
            ];
        })->values();

        // Stat cards
        $totalOrders  = Order::whereDate('created_at', today())->count();
        $pendingCount = Order::where('status', 'pending')->count();
        $menuCount    = $menus->count();

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
            (int) OrderItem::whereHas('menu', fn($q) => $q->where('category', 'makanan'))->sum('quantity'),
            (int) OrderItem::whereHas('menu', fn($q) => $q->where('category', 'minuman'))->sum('quantity'),
            (int) OrderItem::whereHas('menu', fn($q) => $q->where('category', 'snack'))->sum('quantity'),
        ];

        return view('kasir.dashboard', compact(
            'mejas',
            'mejaStatuses',
            'orders',
            'menus',
            'categories',
            'totalOrders',
            'pendingCount',
            'menuCount',
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

    public function storeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'items'         => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric|min:0',
            'total'         => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_number'  => $request->table_number,
            'order_type'    => $request->order_type ?? 'Makan di Sini',
            'status'        => 'pending',
            'total'         => $request->total,
            'note'          => $request->note,
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id'  => $order->id,
                'menu_id'   => $item['menu_id'],
                'quantity'  => $item['quantity'],
                'price'     => $item['price'],
                'subtotal'  => $item['price'] * $item['quantity'],
            ]);
        }

        return response()->json([
            'success'  => true,
            'order_id' => $order->id,
        ]);
    }

    public function destroyMeja($no_meja)
    {
        $meja = Meja::where('no_meja', $no_meja)->firstOrFail();
        $meja->delete();

        return response()->json(['success' => true]);
    }

    // ─── Halaman Manajemen Meja ───────────────────────────────────────────────
    public function mejaIndex()
    {
        $mejas = Meja::all();

        $mejaStatuses = [];
        foreach ($mejas as $meja) {
            $activeOrder = Order::where('table_number', $meja->no_meja)
                ->whereIn('status', ['pending', 'confirmed'])
                ->latest()->first();

            if ($activeOrder?->status === 'confirmed')   $mejaStatuses[$meja->no_meja] = 'aktif';
            elseif ($activeOrder?->status === 'pending') $mejaStatuses[$meja->no_meja] = 'pending';
            else                                         $mejaStatuses[$meja->no_meja] = 'kosong';
        }

        $pendingCount = Order::where('status', 'pending')->count();
        $totalOrders  = Order::whereDate('created_at', today())->count();

        // Tambahkan ini
        $menus = Menu::orderBy('category')->orderBy('name')->get();
        $menuCount = $menus->count();
        $categories = $menus->groupBy('category')->map(function ($items, $name) {
            return (object) [
                'id'          => $name,
                'name'        => ucfirst($name),
                'menus_count' => $items->count(),
            ];
        })->values();

        return view('kasir.meja', compact(
            'mejas', 'mejaStatuses', 'pendingCount',
            'totalOrders', 'menuCount', 'menus', 'categories'
        ));
    }
    // ─── Halaman Riwayat Order ────────────────────────────────────────────────
    public function ordersIndex(Request $request)
    {
        $query = Order::with('items.menu')->orderBy('id', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%'.$request->search.'%')
                ->orWhere('table_number', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->status)     $query->where('status', $request->status);
        if ($request->order_type) $query->where('order_type', $request->order_type);
        if ($request->date)       $query->whereDate('created_at', $request->date);

        $orders         = $query->paginate(15);
        $pendingCount   = Order::where('status', 'pending')->count();
        $completedCount = Order::where('status', 'completed')->count();
        $totalOrders    = Order::whereDate('created_at', today())->count();
        $totalRevenue   = Order::where('status', 'completed')->sum('total');
        $menuCount      = Menu::count();

        return view('kasir.orders', compact(
            'orders', 'pendingCount', 'completedCount',
            'totalOrders', 'totalRevenue', 'menuCount'
        ));
    }
    // ─── Halaman Pembayaran ───────────────────────────────────────────────────
    public function pembayaran($id)
    {
        $order = Order::with('items.menu')->findOrFail($id);

        $pendingCount = Order::where('status', 'pending')->count();
        $totalOrders  = Order::whereDate('created_at', today())->count();
        $menuCount    = Menu::count();

        return view('kasir.pembayaran', compact('order', 'pendingCount', 'totalOrders', 'menuCount'));
    }

    // ─── Proses Pembayaran ────────────────────────────────────────────────────
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris,debit',
            'cash_received'  => 'required|numeric|min:0',
            'change_amount'  => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($id);

        if ($order->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Order sudah selesai.']);
        }

        $order->status         = 'completed';
        $order->payment_method = $request->payment_method;
        $order->cash_received  = $request->cash_received;
        $order->change_amount  = $request->change_amount;
        $order->paid_at        = now();
        $order->save();

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    public function destroy($id)
    {
        $order = \App\Models\Order::findOrFail($id);
    
        // Hapus item-item order dulu (kalau tidak pakai cascade on delete di migration)
        $order->items()->delete();
    
        // Hapus order
        $order->delete();
    
        // Kalau request via AJAX (fetch)
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Order berhasil dihapus.']);
        }
    
        // Fallback redirect (opsional)
        return redirect()->route('kasir.orders')
                        ->with('deleted', 'Order #' . str_pad($id, 5, '0', STR_PAD_LEFT) . ' berhasil dihapus.');
    }
}