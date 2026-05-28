@extends('template.customer')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/myorder.css') }}">
@endpush

@section('content')

<div class="orders-container">

    <h1>Riwayat Pesanan</h1>

    @if($orders->isEmpty())

        <div class="empty">
            Belum ada pesanan
        </div>

    @else

        @foreach($orders as $order)

        <div class="order-card" data-order-id="{{ $order->id }}">

            <div class="top">

                <div>
                    <h2>
                        Meja {{ $order->table_number }}
                    </h2>

                    <p>
                        {{ $order->created_at->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="status {{ $order->status }}">
                    @if($order->status === 'pending')
                        Menunggu konfirmasi
                    @elseif($order->status === 'confirmed')
                        Pesanan sedang diproses
                    @elseif($order->status === 'completed')
                        Pesanan telah diantarkan
                    @else
                        {{ ucfirst($order->status) }}
                    @endif
                </div>

            </div>

            <div class="middle">

                <p>
                    <strong>Total:</strong>
                    Rp {{ number_format($order->total, 0, ',', '.') }}
                </p>

                <p>
                    <strong>Catatan:</strong>
                    {{ $order->note ?: '-' }}
                </p>

            </div>

            <div class="review-note" @if($order->status !== 'completed') style="display:none;" @endif>
                {{ $order->review ? 'Review sudah dikirim.' : 'Review tersedia di halaman konfirmasi pesanan terakhir.' }}
            </div>

        </div>

        @endforeach

    @endif

</div>

@endsection

@push('custom_script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function refreshMyOrders() {
        fetch('/my-orders/status', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;

                data.orders.forEach(order => {
                    const card = document.querySelector('[data-order-id="' + order.id + '"]');
                    if (!card) return;

                    const badge = card.querySelector('.status');
                    badge.textContent = order.status_label;
                    badge.className = 'status ' + order.status;

                    const note = card.querySelector('.review-note');
                    if (order.status === 'completed') {
                        note.textContent = order.reviewed ? 'Review sudah dikirim.' : 'Review tersedia di halaman konfirmasi pesanan terakhir.';
                        note.style.display = 'block';
                    } else {
                        note.style.display = 'none';
                    }
                });
            })
            .catch(() => {});
    }

    refreshMyOrders();
    setInterval(refreshMyOrders, 5000);
});
</script>
@endpush
