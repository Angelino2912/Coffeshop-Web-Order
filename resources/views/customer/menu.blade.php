@extends('template.customer')

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

    <div class="'content-panel">

    @if(session('success'))
        <div class="alert success" style="background: #d4edda; color: #155724; border-left: 4px solid #28a745">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error" style="background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545">{{ session('error') }}</div>
    @endif

    <div class="kelompok" style="margin-bottom: 50px">
        <h2 style="font-size: 1.5rem">Makanan</h2>
        <div class="menu" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr))">
            @foreach($items as $item)
                @if($item->category == 'Makanan')
                    <div class="menu-item">
                        <img src="{{ asset('images/cart-logo.png') }}" alt="Gambar {{ $item['name'] }}" class="image" style="height: 180px">
                        <div class="detail" style="padding: 20px; flex-grow: 1">
                            <h3>{{ $item['name'] }}</h3>
                            <p class="price" style="font-size: 1.2rem; color: #000000; font-weight: bold">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <form action="/cart/add" method="POST" style="padding: 0 20px 20px">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                            <div class="total">
                                <label>Jumlah:</label>
                                <input type="number" name="quantity" value="1" min="1" style="width: 20%; box-sizing: border-box; padding: 4px;">
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

    <div class="kelompok" style="margin-bottom: 50px">
        <h2 style="font-size: 1.5rem">Minuman</h2>
        <div class="menu" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr))">
            @foreach($items as $item)
                @if($item->category == 'Minuman')
                    <div class="menu-item">
                        <img src="{{ asset('images/cart-logo.png') }}" alt="Gambar {{ $item['name'] }}" class="image" style="height: 180px">
                        <div class="detail" style="padding: 20px; flex-grow: 1">
                            <h3>{{ $item['name'] }}</h3>
                            <p class="price" style="font-size: 1.2rem; color: #000000; font-weight: bold">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <form action="/cart/add" method="POST" style="padding: 0 20px 20px">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                            <div class="qty-row">
                                <label>Jumlah:</label>
                                <input type="number" name="quantity" value="1" min="1" style="width: 20%; box-sizing: border-box; padding: 4px;">
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
    </div>
</div>
@endsection