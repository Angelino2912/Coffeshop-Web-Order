@extends('template.customer')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/order.css') }}">
@endpush

@php
    $statusLabel = match ($order->status) {
        'pending' => 'Menunggu konfirmasi',
        'confirmed' => 'Pesanan sedang diproses',
        'completed' => 'Pesanan telah diantarkan',
        default => ucfirst($order->status),
    };
@endphp

@section('content')
<div class="box">
    <a href="/menu" class="back-link">&larr; Kembali ke Menu</a>
    <h1>Pesanan Berhasil Dikonfirmasi</h1>
    <p>Terima kasih! Pesananmu sudah tersimpan dan bisa dibayar sekarang.</p>

    <div class="summary">
        <div class="field"><strong>Nomor Meja:</strong> {{ $order->table_number }}</div>
        <div class="field"><strong>Catatan:</strong> {{ $order->note ?: '-' }}</div>
        <div class="field status-row">
            <strong>Status:</strong>
            <span id="orderStatusBadge" class="status-badge {{ $order->status }}">{{ $statusLabel }}</span>
            <span id="statusSpinner" class="mini-spinner" aria-label="Memuat status"></span>
        </div>
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
                <td>{{ $item->menu?->name ?? 'Menu tidak tersedia' }}</td>
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

    <div id="reviewPanel" class="review-panel" @if($order->status !== 'completed' || $order->review) style="display:none;" @endif>
        <h2>Review Pesanan</h2>
        <p>Pesanan sudah diantarkan. Bagikan pengalaman kamu untuk pesanan ini.</p>

        <form id="reviewForm">
            @csrf
            <div class="rating-group" aria-label="Rating">
                @for($i = 5; $i >= 1; $i--)
                    <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" @if($i === 5) checked @endif>
                    <label for="rating{{ $i }}">&#9733;</label>
                @endfor
            </div>

            <textarea name="comment" rows="4" maxlength="500" placeholder="Tulis komentar kamu (opsional)"></textarea>

            <button type="submit" class="button review-button">
                <span class="button-text">Kirim Review</span>
                <span class="button-spinner" aria-label="Mengirim review"></span>
            </button>
        </form>
    </div>

    <div id="reviewDone" class="review-done" @if(!$order->review) style="display:none;" @endif>
        Terima kasih, review kamu sudah terkirim.
    </div>

    <div style="text-align: center;">
        <a href="/menu" class="button">Tambah Pesanan Lain</a>
    </div>
</div>
@endsection

@push('custom_script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusBadge = document.getElementById('orderStatusBadge');
    const statusSpinner = document.getElementById('statusSpinner');
    const reviewPanel = document.getElementById('reviewPanel');
    const reviewDone = document.getElementById('reviewDone');
    const reviewForm = document.getElementById('reviewForm');

    function setStatus(status, label, canReview, reviewed) {
        statusBadge.textContent = label;
        statusBadge.className = 'status-badge ' + status;

        if (canReview) {
            reviewPanel.style.display = 'block';
            reviewDone.style.display = 'none';
        }

        if (reviewed) {
            reviewPanel.style.display = 'none';
            reviewDone.style.display = 'block';
        }
    }

    function pollOrderStatus() {
        statusSpinner.classList.add('active');

        fetch('/order-confirmation/status', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;
                setStatus(data.status, data.status_label, data.can_review, data.reviewed);
            })
            .catch(() => {})
            .finally(() => {
                statusSpinner.classList.remove('active');
            });
    }

    if (reviewForm) {
        reviewForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const button = reviewForm.querySelector('.review-button');
            button.disabled = true;
            button.classList.add('loading');

            fetch('/order-confirmation/review', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': reviewForm.querySelector('input[name="_token"]').value
                },
                body: new FormData(reviewForm)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reviewPanel.style.display = 'none';
                        reviewDone.textContent = data.message;
                        reviewDone.style.display = 'block';
                    } else if (data.message) {
                        alert(data.message);
                    }
                })
                .catch(() => alert('Review belum terkirim. Coba lagi sebentar.'))
                .finally(() => {
                    button.disabled = false;
                    button.classList.remove('loading');
                });
        });
    }

    pollOrderStatus();
    setInterval(pollOrderStatus, 5000);
});
</script>
@endpush
