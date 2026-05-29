@extends('template.main-kasir')

@push('style')
<link rel="stylesheet" href="{{ asset('style/kasir/kasir.css') }}">
<style>
    .pembayaran-wrap {
        display: flex;
        gap: 24px;
        padding: 24px;
        height: 100%;
        overflow: auto;
    }

    /* ── Struk Kiri ── */
    .struk-panel {
        flex: 1;
        background: var(--white, #fff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 16px;
        padding: 28px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-width: 480px;
    }

    .struk-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .struk-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text1, #111);
    }

    .struk-num {
        font-size: 13px;
        color: var(--text3, #9ca3af);
        margin-top: 2px;
    }

    .struk-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        background: #fef3c7;
        color: #d97706;
    }

    .struk-badge.completed {
        background: #d1fae5;
        color: #059669;
    }

    .struk-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .struk-meta-item {
        background: var(--cream, #f9fafb);
        border-radius: 10px;
        padding: 10px 14px;
    }

    .struk-meta-label {
        font-size: 11px;
        color: var(--text3, #9ca3af);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }

    .struk-meta-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--text1, #111);
    }

    .struk-divider {
        border: none;
        border-top: 1px dashed var(--border, #e5e7eb);
    }

    .struk-order-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: var(--text3, #9ca3af);
    }

    .struk-items {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .struk-item-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .struk-item-thumb {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--cream, #f3f4f6);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .struk-item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .struk-item-body {
        flex: 1;
    }

    .struk-item-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text1, #111);
    }

    .struk-item-qty {
        font-size: 12px;
        color: var(--text3, #9ca3af);
        margin-top: 1px;
    }

    .struk-item-price {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary, #1a5c38);
    }

    .struk-totals {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .struk-total-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: var(--text2, #6b7280);
    }

    .struk-total-row.grand {
        font-size: 17px;
        font-weight: 800;
        color: var(--text1, #111);
        padding-top: 10px;
        border-top: 2px solid var(--border, #e5e7eb);
    }

    /* ── Panel Pembayaran Kanan ── */
    .pay-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .pay-card {
        background: var(--white, #fff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 16px;
        padding: 24px;
    }

    .pay-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text1, #111);
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    /* Metode Pembayaran */
    .pay-methods {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .pay-method-btn {
        border: 2px solid var(--border, #e5e7eb);
        border-radius: 12px;
        padding: 14px 8px;
        background: var(--white, #fff);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        transition: all .15s;
    }

    .pay-method-btn:hover {
        border-color: var(--primary, #1a5c38);
        background: #f0fdf4;
    }

    .pay-method-btn.active {
        border-color: var(--primary, #1a5c38);
        background: #f0fdf4;
    }

    .pay-method-btn i {
        font-size: 22px;
        color: var(--primary, #1a5c38);
    }

    .pay-method-btn span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text1, #111);
    }

    /* Input Tunai */
    .pay-cash-section {
        margin-top: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pay-input-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text2, #6b7280);
        margin-bottom: 4px;
    }

    .pay-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        color: var(--text1, #111);
        background: var(--cream, #f9fafb);
        box-sizing: border-box;
        outline: none;
        transition: border-color .15s;
    }

    .pay-input:focus {
        border-color: var(--primary, #1a5c38);
        background: #fff;
    }

    .pay-quick-btns {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pay-quick-btn {
        padding: 6px 14px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: var(--cream, #f9fafb);
        color: var(--text2, #6b7280);
        cursor: pointer;
        transition: all .15s;
    }

    .pay-quick-btn:hover {
        border-color: var(--primary, #1a5c38);
        color: var(--primary, #1a5c38);
        background: #f0fdf4;
    }

    /* Kembalian */
    .pay-change-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pay-change-box.minus {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .pay-change-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text2, #6b7280);
    }

    .pay-change-val {
        font-size: 20px;
        font-weight: 800;
        color: #059669;
    }

    .pay-change-val.minus {
        color: #dc2626;
    }

    /* Tombol Proses */
    .pay-submit-btn {
        width: 100%;
        padding: 16px;
        background: var(--primary, #1a5c38);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: opacity .15s;
        margin-top: 4px;
    }

    .pay-submit-btn:hover { opacity: .9; }
    .pay-submit-btn:disabled { opacity: .5; cursor: not-allowed; }

    .pay-back-link {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text3, #9ca3af);
        text-decoration: none;
        margin-bottom: 4px;
        transition: color .15s;
    }

    .pay-back-link:hover { color: var(--primary, #1a5c38); }

    /* Success overlay */
    .pay-success-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }

    .pay-success-overlay.show {
        display: flex;
    }

    .pay-success-modal {
        background: #fff;
        border-radius: 20px;
        padding: 40px 32px;
        text-align: center;
        max-width: 360px;
        width: 90%;
        animation: popIn .3s ease;
    }

    @keyframes popIn {
        from { transform: scale(.85); opacity: 0; }
        to   { transform: scale(1);  opacity: 1; }
    }

    .pay-success-icon {
        width: 72px;
        height: 72px;
        background: #d1fae5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
        color: #059669;
    }

    .pay-success-title {
        font-size: 20px;
        font-weight: 800;
        color: #111;
        margin-bottom: 6px;
    }

    .pay-success-sub {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 28px;
    }

    .pay-success-change {
        background: #f0fdf4;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 700;
        color: #059669;
        margin-bottom: 24px;
    }

    .pay-success-btn {
        width: 100%;
        padding: 13px;
        background: var(--primary, #1a5c38);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }
</style>
@endpush

@section('title', 'Pembayaran')

@section('content')

@php
    $sub   = $order->items->sum(fn($i) => $i->price * $i->quantity);
    $tax   = round($sub * 0.1);
    $total = $order->total;
@endphp

<div class="pembayaran-wrap">

    {{-- ── Struk Kiri ── --}}
    <div class="struk-panel">

        <a href="{{ route('kasir.dashboard') }}" class="pay-back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Kasir
        </a>

        <div class="struk-header">
            <div>
                <div class="struk-title">Struk Pesanan</div>
                <div class="struk-num">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
            <span class="struk-badge {{ $order->status === 'completed' ? 'completed' : '' }}">
                {{ match($order->status) {
                    'pending'   => 'Menunggu',
                    'confirmed' => 'Diproses',
                    'completed' => 'Selesai',
                    default     => ucfirst($order->status)
                } }}
            </span>
        </div>

        <div class="struk-meta">
            <div class="struk-meta-item">
                <div class="struk-meta-label">Pelanggan</div>
                <div class="struk-meta-value">{{ $order->customer_name }}</div>
            </div>
            <div class="struk-meta-item">
                <div class="struk-meta-label">Tipe Order</div>
                <div class="struk-meta-value">{{ $order->order_type ?? 'Makan di Sini' }}</div>
            </div>
            @if($order->table_number)
            <div class="struk-meta-item">
                <div class="struk-meta-label">Meja</div>
                <div class="struk-meta-value">{{ $order->table_number }}</div>
            </div>
            @endif
            <div class="struk-meta-item">
                <div class="struk-meta-label">Waktu</div>
                <div class="struk-meta-value">{{ $order->created_at->format('H:i') }}</div>
            </div>
        </div>

        <hr class="struk-divider">

        <div class="struk-order-label">Daftar Pesanan</div>
        <div class="struk-items">
            @foreach($order->items as $item)
            <div class="struk-item-row">
                <div class="struk-item-thumb">
                    @if($item->menu?->image)
                        <img src="{{ asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}">
                    @else
                        ☕
                    @endif
                </div>
                <div class="struk-item-body">
                    <div class="struk-item-name">{{ $item->menu?->name ?? 'Menu dihapus' }}</div>
                    <div class="struk-item-qty">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</div>
                </div>
                <div class="struk-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        <hr class="struk-divider">

        <div class="struk-totals">
            <div class="struk-total-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($sub, 0, ',', '.') }}</span>
            </div>
            <div class="struk-total-row">
                <span>Pajak (10%)</span>
                <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
            </div>
            <div class="struk-total-row grand">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    {{-- ── Panel Pembayaran Kanan ── --}}
    <div class="pay-panel">

        @if($order->status === 'completed')
        {{-- Sudah dibayar --}}
        <div class="pay-card" style="text-align:center; padding: 48px 24px;">
            <div style="width:64px;height:64px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#059669;">
                <i class="bi bi-check-lg"></i>
            </div>
            <div style="font-size:18px;font-weight:800;color:#111;margin-bottom:8px;">Pesanan Sudah Dibayar</div>
            <div style="font-size:13px;color:#6b7280;margin-bottom:8px;">
                Metode: <strong>{{ strtoupper($order->payment_method ?? '-') }}</strong>
            </div>
            @if($order->cash_received)
            <div style="font-size:13px;color:#6b7280;">
                Bayar: <strong>Rp {{ number_format($order->cash_received, 0, ',', '.') }}</strong> &nbsp;|&nbsp;
                Kembalian: <strong>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</strong>
            </div>
            @endif
            <a href="{{ route('kasir.dashboard') }}" class="pay-submit-btn" style="margin-top:28px;display:flex;">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Kasir
            </a>
        </div>

        @else
        {{-- Form Pembayaran --}}
        <div class="pay-card">
            <div class="pay-card-title">Metode Pembayaran</div>
            <div class="pay-methods">
                <button class="pay-method-btn active" data-method="cash" onclick="setMethod(this)">
                    <i class="bi bi-cash-stack"></i>
                    <span>Tunai</span>
                </button>
            </div>

            {{-- Bagian tunai --}}
            <div class="pay-cash-section" id="cashSection">
                <div>
                    <div class="pay-input-label">Uang Diterima</div>
                    <input class="pay-input"
                           type="number"
                           id="cashReceived"
                           placeholder="0"
                           min="0"
                           oninput="calcChange()">
                </div>
                <div class="pay-quick-btns" id="quickBtns">
                    {{-- diisi JS berdasarkan total --}}
                </div>
            </div>
        </div>

        <div class="pay-card" id="changeCard">
            <div class="pay-card-title">Kembalian</div>
            <div class="pay-change-box" id="changeBox">
                <span class="pay-change-label">Kembalian</span>
                <span class="pay-change-val" id="changeVal">Rp 0</span>
            </div>
        </div>

        <button class="pay-submit-btn" id="submitBtn" onclick="processPayment()" disabled>
            <i class="bi bi-check-circle"></i>
            Proses Pembayaran
        </button>
        @endif

    </div>

</div>

{{-- Success Modal --}}
<div class="pay-success-overlay" id="successOverlay">
    <div class="pay-success-modal">
        <div class="pay-success-icon"><i class="bi bi-check-lg"></i></div>
        <div class="pay-success-title">Pembayaran Berhasil!</div>
        <div class="pay-success-sub">Pesanan #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} telah selesai.</div>
        <div class="pay-success-change" id="modalChange">Kembalian: Rp 0</div>
        <button class="pay-success-btn" onclick="window.location.href='{{ route('kasir.orders') }}'">
            Lihat Riwayat Order
        </button>
    </div>
</div>

@endsection

@push('custom_script')
<script>
var TOTAL        = {{ $total }};
var activeMethod = 'cash';

// Tombol nominal cepat
var quicks = [TOTAL, roundUp(TOTAL, 5000), roundUp(TOTAL, 10000), roundUp(TOTAL, 50000)];
quicks = [...new Set(quicks)].slice(0, 4);

document.getElementById('quickBtns').innerHTML = quicks.map(function(v) {
    return '<button class="pay-quick-btn" onclick="setQuick(' + v + ')">Rp ' + v.toLocaleString('id-ID') + '</button>';
}).join('');

function roundUp(n, to) { return Math.ceil(n / to) * to; }

function setMethod(btn) {
    document.querySelectorAll('.pay-method-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    activeMethod = btn.dataset.method;

    var cashSection = document.getElementById('cashSection');
    var changeCard  = document.getElementById('changeCard');

    if (activeMethod === 'cash') {
        cashSection.style.display = '';
        changeCard.style.display  = '';
        calcChange();
    } else {
        // QRIS / Debit — langsung bisa proses
        cashSection.style.display = 'none';
        changeCard.style.display  = 'none';
        document.getElementById('submitBtn').disabled = false;
    }
}

function setQuick(val) {
    document.getElementById('cashReceived').value = val;
    calcChange();
}

function calcChange() {
    var received = parseFloat(document.getElementById('cashReceived').value) || 0;
    var change   = received - TOTAL;
    var box      = document.getElementById('changeBox');
    var val      = document.getElementById('changeVal');
    var btn      = document.getElementById('submitBtn');

    val.textContent = 'Rp ' + Math.abs(change).toLocaleString('id-ID');

    if (change < 0) {
        box.classList.add('minus');
        val.classList.add('minus');
        val.textContent = '−Rp ' + Math.abs(change).toLocaleString('id-ID');
        btn.disabled = true;
    } else {
        box.classList.remove('minus');
        val.classList.remove('minus');
        btn.disabled = false;
    }
}

function processPayment() {
    var btn      = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

    var cashReceived = activeMethod === 'cash'
        ? parseFloat(document.getElementById('cashReceived').value) || 0
        : TOTAL;

    var changeAmount = activeMethod === 'cash'
        ? cashReceived - TOTAL
        : 0;

    fetch('{{ route("kasir.payment.process", $order->id) }}', {
        method  : 'POST',
        headers : {
            'Content-Type' : 'application/json',
            'Accept'       : 'application/json',
            'X-CSRF-TOKEN' : '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_method : activeMethod,
            cash_received  : cashReceived,
            change_amount  : changeAmount
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('modalChange').textContent =
                'Kembalian: Rp ' + changeAmount.toLocaleString('id-ID');
            document.getElementById('successOverlay').classList.add('show');
        } else {
            alert(data.message || 'Gagal memproses pembayaran.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Proses Pembayaran';
        }
    })
    .catch(function() {
        alert('Terjadi kesalahan. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Proses Pembayaran';
    });
}
</script>
@endpush