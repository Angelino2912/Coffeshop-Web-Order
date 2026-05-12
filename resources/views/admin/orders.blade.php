@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/order.css') }}">
@endpush

@section('content')

<div class="container">

    <h1>Daftar Pesanan</h1>

    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif

    @foreach($orders as $order)

    <div class="order-card">

        <h2>
            {{ $order->customer_name }}
        </h2>

        <p>
            Nomor Meja:
            {{ $order->table_number }}
        </p>

        <p>
            Total:
            Rp {{ number_format($order->total) }}
        </p>

        <p>
            Catatan:
            {{ $order->note }}
        </p>

        <p>
            Status:
            <strong>
                {{ $order->status }}
            </strong>
        </p>

        <form
            action="/admin/orders/{{ $order->id }}/status"
            method="POST"
        >

            @csrf
            @method('PUT')

            <select name="status">

                <option value="pending">
                    Pending
                </option>

                <option value="confirmed">
                    Diproses
                </option>

                <option value="completed">
                    Selesai
                </option>

            </select>

            <button type="submit">
                Update Status
            </button>

        </form>

    </div>

    @endforeach

</div>

@endsection