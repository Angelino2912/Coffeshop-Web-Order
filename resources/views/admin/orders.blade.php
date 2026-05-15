@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/order.css') }}">
@endpush

@section('content')

<div class="container">

    <div class="page-header">
        <h1>Daftar Order</h1>
        <p>List pesanan masuk dengan detail menu.</p>
    </div>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <div class="order-list">
        @forelse($orders as $order)
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <h2>{{ $order->customer_name }}</h2>
                        <p class="order-meta">Meja {{ $order->table_number }} &bullet; {{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <span class="order-status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>

                <div class="order-items">
                    <h3>Menu</h3>
                    <ul>
                        @foreach($order->items as $item)
                            <li>
                                <span class="item-name">{{ $item->menu?->name ?? 'Menu tidak tersedia' }}</span>
                                <span class="item-qty">x{{ $item->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if($order->note)
                    <div class="order-note">
                        <strong>Catatan:</strong> {{ $order->note }}
                    </div>
                @endif

                <div class="order-footer">
                    <div class="order-total">Total: Rp {{ number_format($order->total) }}</div>

                    <form class="status-form" action="/admin/orders/{{ $order->id }}/status" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="status">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="order-empty">
                Belum ada pesanan masuk.
            </div>
        @endforelse
    </div>

</div>

@endsection