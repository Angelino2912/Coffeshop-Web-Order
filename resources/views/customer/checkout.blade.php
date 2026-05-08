@extends('template.main')

@section('content')
<div class="checkout-container">
    <div class="checkout-box">
        <div class="checkout-left">
            <h1>Konfirmasi Pesanan</h1>
            <p>Data pelanggan dan nomor meja sudah diambil dari login awal. Segera konfirmasi pesanan Anda tanpa mengisi ulang informasi tersebut.</p>
        </div>
        <div class="checkout-right">
            @if(session('error'))
                <div class="error">{{ session('error') }}</div>
            @endif

            <h2>Ringkasan Pesanan</h2>
            <table class="checkout-table">
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

            <div class="checkout-summary">
                <h3>Data Customer</h3>
                <p><strong>Nama:</strong> {{ $customer->nama ?? $customer->name ?? 'Tamu' }}</p>
                <p><strong>Nomor Meja:</strong> {{ $customer->no_meja ?? $customer->table_number ?? '-' }}</p>
            </div>

            <form action="/checkout" method="POST">
                @csrf
                <div class="checkout-note">
                    <label>Catatan Pesanan (opsional)</label>
                    <textarea name="note" rows="4" placeholder="Contoh: Kurang pedas"></textarea>
                </div>
                <button type="submit" style="margin-top: 16px;">Bayar & Konfirmasi</button>
            </form>
        </div>
    </div>
</div>
@endsection
