@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/dashboard.css') }}">
@endpush

@section('title', 'Dashboard')

@section('content')

<div class="content">

    <div class="header">
        <h1>Dashboard</h1>
        <p>Hi Angeline, Welcome to Cinta Rasa</p>
    </div>

    <div class="dashboard-grid">

        <div class="left-panel">
            <div class="meja-section">
                <h2>Tambah Meja</h2>
                <form method="POST" action="/admin/meja/store" class="meja-form">
                    @csrf
                    <input type="text" name="no_meja" placeholder="Contoh: A1" required>
                    <button type="submit">Tambah Meja</button>
                </form>
                <form method="POST" action="/admin/meja/generate-qr">
                    @csrf
                    <button type="submit" class="qr-button">Generate QR Semua Meja</button>
                </form>
            </div>

            <div class="meja-list">
                @foreach($mejas as $meja)
                <div class="meja-card">
                    <h3>Meja {{ $meja->no_meja }}</h3>
                    @if($meja->qr_uuid)
                        <img src="{{ asset('storage/qr/meja_' . $meja->no_meja . '.svg') }}" width="150">
                    @else
                        <p>QR belum dibuat</p>
                    @endif
                    <p class="uuid-text">{{ $meja->qr_uuid }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="right-panel">
            <div class="order-list-section">
                <h2>Order Masuk</h2>
                @forelse($orders as $order)
                    <div class="order-card">
                        <h2>{{ $order->customer_name }}</h2>
                        <p>Meja: {{ $order->table_number }}</p>
                        @if($order->items->count())
                            <p><strong>Pesanan:</strong></p>
                            <ul class="order-items">
                                @foreach($order->items as $item)
                                    <li>{{ $item->menu?->name ?? 'Menu tidak tersedia' }} x {{ $item->quantity }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <p>Total: Rp {{ number_format($order->total) }}</p>
                        <p>Status: <strong>{{ $order->status }}</strong></p>
                        <form action="/admin/orders/{{ $order->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Diproses</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            <button type="submit">Update Status</button>
                        </form>
                    </div>
                @empty
                    <div class="order-card"><p>Tidak ada order masuk.</p></div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection