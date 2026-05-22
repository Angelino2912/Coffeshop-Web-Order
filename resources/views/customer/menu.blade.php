@extends('template.customer')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/menu.css') }}">
@endpush

@section('content')

<div class="hero-bg"></div>

<div class="container">

    <div class="nav">
        <a href="/dashboard">Dashboard</a>
        <a href="/cart">Keranjang</a>
        <a href="/checkout">Checkout</a>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    {{-- MAKANAN --}}
    <div class="kelompok">
        <h2>Makanan</h2>
        <div class="menu">
            @foreach($items as $item)
                @if($item->category == 'Makanan')
                    <div class="menu-item">
                        <div class="img-wrap">
                            <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/cart-logo.png') }}"
                                alt="{{ $item->name }}" class="image">
                            <span class="price-badge">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail">
                            <h3>{{ $item->name }}</h3>
                        </div>
                        <form action="/cart/add" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="total">
                                <label>Jumlah:</label>
                                <input type="number" name="quantity" value="1" min="1">
                                <button type="submit" class="btn-cart">+ Keranjang</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- MINUMAN --}}
    <div class="kelompok">
        <h2>Minuman</h2>
        <div class="menu">
            @foreach($items as $item)
                @if($item->category == 'Minuman')
                    <div class="menu-item">
                        <div class="img-wrap">
                            <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/cart-logo.png') }}"
                                alt="{{ $item->name }}" class="image">
                            <span class="price-badge">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail">
                            <h3>{{ $item->name }}</h3>
                        </div>
                        <form action="/cart/add" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="qty-row">
                                <label>Jumlah:</label>
                                <input type="number" name="quantity" value="1" min="1">
                                <button type="submit" class="btn-cart">+ Keranjang</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

</div>
@endsection