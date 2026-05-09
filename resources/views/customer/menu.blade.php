@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/menu.css') }}">
@endpush

@section('content')
<div class="container">

    <div class="nav">
        <a href="/dashboard">Dashboard</a>
        <a href="/cart">Keranjang</a>
        <a href="/checkout">Checkout</a>
    </div>

    <!-- <h1>Menu Makanan & Minuman</h1> -->

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <div class="kelompok">
        <h2>Makanan</h2>
        <div class="menu">
            @foreach($items as $item)
                @if($item->category == 'Makanan')
                    <div class="menu-item">
                        <img src="{{ asset('images/cart-logo.png') }}" alt="Gambar {{ $item['name'] }}" class="image">
                        <div class="detail">
                            <h3>{{ $item['name'] }}</h3>
                            <p class="price">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <form action="/cart/add" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                            <div class="total">
                                <label>Jumlah:</label>
                                <input type="number" name="quantity" value="1" min="1">
                            </div>
                            <button type="submit" class="btn-cart">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="kelompok">
        <h2>Minuman</h2>
        <div class="menu">
            @foreach($items as $item)
                @if($item->category == 'Minuman')
                    <div class="menu-item">
                        <img src="{{ asset('images/cart-logo.png') }}" alt="Gambar {{ $item['name'] }}" class="image">
                        <div class="detail">
                            <h3>{{ $item['name'] }}</h3>
                            <p class="price">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <form action="/cart/add" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                            <div class="qty-row">
                                <label>Jumlah:</label>
                                <input type="number" name="quantity" value="1" min="1">
                            </div>
                            <button type="submit" class="btn-cart">
                                🛒 Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

</div>
@endsection