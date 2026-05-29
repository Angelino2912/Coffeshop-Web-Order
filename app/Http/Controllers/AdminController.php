<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Meja;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;
use App\Models\Review;

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
        $mejas = Meja::orderByRaw('CAST(no_meja AS UNSIGNED), no_meja')->get();

        $ordersToday = Order::whereDate('created_at', today())->get();

        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date           = now()->subDays($i);
            $weeklyLabels[] = $date->locale('id')->isoFormat('ddd');
            $weeklyData[]   = (int) Order::whereDate('created_at', $date)->sum('total');
        }

        $categoryData = [
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->whereIn('category', ['makanan', 'Makanan']))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->whereIn('category', ['minuman', 'Minuman']))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->whereIn('category', ['snack', 'Snack']))->sum('quantity'),
        ];

        return view('admin.dashboard', compact(
            'mejas',
            'ordersToday',
            'weeklyLabels',
            'weeklyData',
            'categoryData',
        ));
    }

    public function manajemenMeja()
    {
        $mejas = Meja::orderByRaw('CAST(no_meja AS UNSIGNED), no_meja')->get();

        return view('admin.manajemen-meja', compact('mejas'));
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
        $data = $request->validate([
            'no_meja_awal' => 'required|integer|min:1',
            'jumlah_meja'  => 'required|integer|min:1|max:100',
        ]);

        $dibuat = 0;

        for ($i = 0; $i < $data['jumlah_meja']; $i++) {
            $noMeja = (string) ($data['no_meja_awal'] + $i);

            if (Meja::where('no_meja', $noMeja)->exists()) {
                continue;
            }

            Meja::create(['no_meja' => $noMeja]);
            $dibuat++;
        }

        return back()->with('success', $dibuat . ' meja berhasil ditambahkan');
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

    public function reviews()
    {
        $reviews = Review::with('order.items.menu')->latest()->get();

        return view('admin.reviews', compact('reviews'));
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
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->whereIn('category', ['makanan', 'Makanan']))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->whereIn('category', ['minuman', 'Minuman']))->sum('quantity'),
            (int) \App\Models\OrderItem::whereHas('menu', fn($q) => $q->whereIn('category', ['snack', 'Snack']))->sum('quantity'),
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
