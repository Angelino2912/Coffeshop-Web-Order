@extends('template.customer')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/cart.css') }}">
@endpush

@section('content')
<div class="box">
    <a href="/menu" style="color: #5c3a21; text-decoration: none; font-weight: 700;">← Kembali ke Menu</a>
    <h1 style="font-size: 32px; margin: 20px 0 30px; color: #2e1f0b;">Keranjang Pesanan</h1>

    @if(session('success'))
        <div class="alert success" style="background: #d4edda; color: #155724; border-left: 4px solid #28a745">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error" style="background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545">{{ session('error') }}</div>
    @endif

    @if(empty($cart))
        <p>Keranjang kamu kosong. Silakan pilih menu terlebih dahulu.</p>
    @else
        <table>
            <thead style="background: #5c3a21; color: white;">
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $item)
                    <tr style="border-bottom: 1px solid #f0ece8">
                        <td>{{ $item['name'] }}</td>
                        <td>
                            <input type="hidden" id="{{ "price_".$item['id'] }}" value="{{ $item['price'] }}">   
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </td>
                        <td>
                            <form action="/cart/update" method="POST" class="form-inline">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                                <input type="number" onchange="update_harga('{{ $item['id'] }}')" name="quantity" id="qty_{{ $item['id'] }}" value="{{ $item['quantity'] }}" min="0" style="width:60px;">
                                <button type="submit" class="button">Perbarui</button>
                            </form>
                        </td>
                        <td id="total_harga_{{ $item['id'] }}">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                        <td>
                            <form action="/cart/remove" method="POST">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                                <button type="submit" class="button">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot style=" background: #f7f0ea; font-size: 16px;">
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <a href="/checkout" class="actions">Lanjut ke Checkout</a>
    @endif
</div>
@endsection
@push('custom_script')
<script>
    function update_harga(id) {
        var qty_item = document.getElementById('qty_'+id).value
        var barang_price = document.getElementById('price_'+id).value
        var total_harga = parseInt(qty_item) * parseInt(barang_price)
        const formatRupiah = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0 
        }).format(total_harga);

        document.getElementById('total_harga_'+id).innerText =formatRupiah

        console.log(qty_item)
        console.log(barang_price)
    }
</script>
@endpush
