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
                            <input type="hidden" id="price_{{ $item['id'] }}" value="{{ $item['price'] }}">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </td>
                        <td>
                            <input type="number"
                                   id="qty_{{ $item['id'] }}"
                                   value="{{ $item['quantity'] }}"
                                   min="0"
                                   style="width:60px;"
                                   onchange="updateCart('{{ $item['id'] }}', this.value)">
                        </td>
                        <td id="total_harga_{{ $item['id'] }}" data-raw="{{ $item['price'] * $item['quantity'] }}">
                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </td>
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
            <tfoot style="background: #f7f0ea; font-size: 16px;">
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2"><strong id="grand_total">Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
        <a href="/checkout" class="actions">Lanjut ke Checkout</a>
    @endif
</div>
@endsection

@push('custom_script')
<script>
    function updateCart(itemId, qty) {
        qty = parseInt(qty) || 0;

        // Update subtotal langsung di UI
        var price    = parseInt(document.getElementById('price_' + itemId).value) || 0;
        var subtotal = qty * price;
        var subtotalEl = document.getElementById('total_harga_' + itemId);

        if (qty === 0) {
            // Hapus baris dari tampilan jika qty 0
            subtotalEl.closest('tr').remove();
        } else {
            subtotalEl.dataset.raw = subtotal;
            subtotalEl.innerText   = formatRupiah(subtotal);
        }

        hitung_grand_total();

        // Kirim ke server via AJAX
        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ item_id: itemId, quantity: qty })
        });
    }

    function hitung_grand_total() {
        var semua = document.querySelectorAll('[id^="total_harga_"]');
        var total = 0;
        semua.forEach(function(el) {
            total += parseInt(el.dataset.raw) || 0;
        });
        document.getElementById('grand_total').innerText = formatRupiah(total);
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }
</script>
@endpush