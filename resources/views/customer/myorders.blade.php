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

        <div class="order-card">

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
                    {{ $order->status }}
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

        </div>

        @endforeach

    @endif

</div>

@endsection