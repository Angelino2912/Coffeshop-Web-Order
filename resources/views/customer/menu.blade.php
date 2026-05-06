@extends('template.main')

@section('content')
<div class="box">
    <div class="top-links">
        <a href="/dashboard">Dashboard</a>
        <a href="/cart">Keranjang</a>
        <a href="/checkout">Checkout</a>
    </div>
    <h1>Menu Makanan & Minuman</h1>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <div class="menu-grid">
        @foreach($items as $item)
            <div class="item">
                <h3>{{ $item['name'] }}</h3>
                <p><strong>Kategori:</strong> {{ $item['category'] }}</p>
                <p><strong>Harga:</strong> Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                <form action="/cart/add" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                    <label>Jumlah:</label>
                    <input type="number" name="quantity" value="1" min="1" style="width:60px; margin-left:8px;">
                    <button type="submit">Tambah ke Keranjang</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection