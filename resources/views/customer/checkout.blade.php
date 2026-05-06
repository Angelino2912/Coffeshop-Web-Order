@extends('template.main')

@section('content')
<div class="box">
    <a href="/cart">← Kembali ke Keranjang</a>
    <h1>Konfirmasi Pesanan</h1>

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <h2>Ringkasan Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Bayar</strong></td>
                <td><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <form action="/checkout" method="POST" style="margin-top:24px;">
        @csrf
        <div class="form-group">
            <label>Nama Customer</label>
            <input type="text" name="name" placeholder="Masukkan nama" required>
        </div>
        <div class="form-group">
            <label>Nomor HP</label>
            <input type="text" name="phone" placeholder="08xxxx" required>
        </div>
        <div class="form-group">
            <label>Nomor Meja</label>
            <input type="text" name="table_number" placeholder="Nomor meja" required>
        </div>
        <div class="form-group">
            <label>Catatan Pesanan (opsional)</label>
            <textarea name="note" rows="4" placeholder="Contoh: Kurang pedas" ></textarea>
        </div>
        <button type="submit">Bayar & Konfirmasi</button>
    </form>
</div>
@endsection
