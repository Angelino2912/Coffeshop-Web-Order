<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Meja;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;

class AdminController extends Controller
{
    public function orders()
    {
        $orders = Order::with('items.menu')->latest()->get();

        return view('admin.orders', [
            'orders' => $orders
        ]);
    }

    public function dashboard()
    {
        $mejas = Meja::all();

        $ordersToday = Order::whereDate('created_at', today())->get();

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

        return view('admin.dashboard', compact(
            'mejas',
            'orders',
            'ordersToday',
            'weeklyLabels',
            'weeklyData',
            'categoryData',
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $order         = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json(['success' => true, 'status' => $order->status]);
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

        return redirect()->back()->with('success', 'QR semua meja berhasil digenerate');
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

    public function manajemenMenu()
    {
        $items = Menu::all();

        return view('admin.manajemen-menu', [
            'items' => $items,
        ]);
    }

    // ✅ DIUPDATE: tambah handle upload foto
    public function storeMenu(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'category' => 'required',
            'price'    => 'required|integer',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('name', 'category', 'price');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        Menu::create($data);

        return back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // ✅ DIUPDATE: tambah handle upload foto, foto lama otomatis terhapus
    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $menu = Menu::findOrFail($id);
        $data = $request->only('name', 'category', 'price');

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $menu->update($data);

        return back()->with('success', 'Menu berhasil diupdate!');
    }

    public function destroyMenu($id)
    {
        Menu::findOrFail($id)->delete();

        return back()->with('success', 'Menu berhasil dihapus!');
    }

    public function analytics()
    {
        $totalPendapatan = Order::where('status', 'completed')->sum('total');
        $totalOrder      = Order::count();
        $rataRata        = $totalOrder > 0 ? $totalPendapatan / $totalOrder : 0;
        $totalItem       = \App\Models\OrderItem::sum('quantity');

        $recentOrders = Order::latest()->get();

        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date           = now()->subDays($i);
            $weeklyLabels[] = $date->locale('id')->isoFormat('ddd');
            $weeklyData[]   = (int) Order::whereDate('created_at', $date)->sum('total');
        }

        $monthlyLabels = [];
        $monthlyData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $date            = now()->subMonths($i);
            $monthlyLabels[] = $date->locale('id')->isoFormat('MMM YY');
            $monthlyData[]   = (int) Order::whereYear('created_at', $date->year)
                                          ->whereMonth('created_at', $date->month)
                                          ->sum('total');
        }

        $categoryData = [
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->where('category', 'makanan'))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->where('category', 'minuman'))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->where('category', 'snack'))->sum('quantity'),
        ];

        $topMenus = \App\Models\OrderItem::with('menu')
            ->selectRaw('menu_id, SUM(quantity) as total_qty')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn($item) => (object)[
                'name'      => $item->menu?->name ?? 'Menu dihapus',
                'category'  => $item->menu?->category ?? '-',
                'total_qty' => $item->total_qty,
            ]);

        return view('admin.analytics', compact(
            'totalPendapatan',
            'totalOrder',
            'rataRata',
            'totalItem',
            'recentOrders',
            'weeklyLabels',
            'weeklyData',
            'monthlyLabels',
            'monthlyData',
            'categoryData',
            'topMenus',
        ));
    }
}