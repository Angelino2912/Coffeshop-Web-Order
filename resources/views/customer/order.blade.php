@extends('template.main')

@section('content')
<div class="box">
    <a href="/dashboard">← Kembali ke Dashboard</a>
    <h1>Pesanan Berhasil Dikonfirmasi</h1>
    <p>Terima kasih! Pesananmu sudah tersimpan dan bisa dibayar sekarang.</p>

    <div class="summary">
        <div class="field"><strong>Nama:</strong> {{ $order->customer_name }}</div>
        <div class="field"><strong>Nomor HP:</strong> {{ $order->phone }}</div>
        <div class="field"><strong>Nomor Meja:</strong> {{ $order->table_number }}</div>
        <div class="field"><strong>Catatan:</strong> {{ $order->note ?: '-' }}</div>
    </div>

    <h2>Detail Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->menu->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Bayar</strong></td>
                <td><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <a href="/menu" class="button">Tambah Pesanan Lain</a>
</div>
@endsection