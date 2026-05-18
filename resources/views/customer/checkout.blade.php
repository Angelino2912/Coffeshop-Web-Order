@extends('template.customer')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/checkout.css') }}">
@endpush

@section('content')
<div class="checkout-container">
    <div class="checkout-box">
        <div class="checkout-header" style="display: flex; flex-direction: column; gap: 24px;">
            <h1 style="font-size: 32px; color: #2e1f0b;">Konfirmasi Pesanan</h1>
            <p style="color: #5c3a21; line-height: 1.8; font-size: 16px;">Data pelanggan dan nomor meja sudah diambil dari login awal. Segera konfirmasi pesanan Anda tanpa mengisi ulang informasi tersebut.</p>
        </div>

        <div class="checkout-content" style="display: flex; flex-direction: column; gap: 24px;">
            @if(session('error'))
                <div class="error">{{ session('error') }}</div>
            @endif

            <h2 style="font-size: 26px; margin-bottom: 14px; color: #2e1f0b;">Ringkasan Pesanan</h2>
            <table class="checkout-table" style="width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 18px; box-shadow: 0 14px 30px rgba(0,0,0,0.08);">
                <thead style=" background: #5c3a21; color: white;">
                    <tr>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $item)
                    <tr style="border-bottom: 1px solid rgba(92,58,33,0.09);">
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background: #f7f0ea;">
                    <tr>
                        <td colspan="2" style=" font-weight: 700; font-size: 15px;"><strong>Total Bayar</strong></td>
                        <td><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="checkout-summary" style="background: #f7f0ea; border-radius: 20px; padding: 20px; border: 1px solid rgba(92,58,33,0.12);">
                <h3 style="margin-bottom: 14px;font-size: 20px;color: #3d2714;">Data Customer</h3>
                <p style="margin-bottom: 10px;color: #5c3a21; font-size: 15px;   line-height: 1.7;"><strong>Nama:</strong> {{ $customer->nama ?? $customer->name ?? 'Tamu' }}</p>
                <p style="margin-bottom: 10px;color: #5c3a21; font-size: 15px;line-height: 1.7;"><strong>Nomor Meja:</strong> {{ $customer->no_meja ?? $customer->table_number ?? '-' }}</p>
            </div>

            <form action="/checkout" method="POST">
                @csrf
                <div class="checkout-note" style="display: flex; flex-direction: column; gap: 10px;">
                    <label style=" font-weight: 700; color: #2e1f0b;">Catatan Pesanan (opsional)</label>
                    <textarea name="note" rows="4" placeholder="Contoh: Kurang pedas"></textarea>
                </div>
                <button type="submit" style="margin-top: 16px;">Bayar & Konfirmasi</button>
            </form>
        </div>
    </div>
</div>
@endsection
