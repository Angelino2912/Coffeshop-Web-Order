@extends('template.main-kasir')

@push('style')
<link rel="stylesheet" href="{{ asset('style/kasir/kasir.css') }}">
@endpush

@section('title', 'Dashboard Kasir')

@section('content')

{{-- ===== MAIN: Menu Panel ===== --}}
<main class="kasir-main">

    {{-- Search --}}
    <div class="kasir-searchbar">
        <i class="bi bi-search"></i>
        <input class="kasir-search-input"
               type="text"
               id="menuSearch"
               placeholder="Cari menu..."
               oninput="filterMenu(this.value)">
        <span class="kasir-search-kbd">⌘K</span>
    </div>

    {{-- Category Pills --}}
    <div class="kasir-category-row" id="categoryRow">

        {{-- Semua --}}
        <div class="cat-card cat-card-coffee active" data-cat="semua" onclick="setCategory(this, 'semua')">
            <span class="cat-status-badge badge-avail-white">Tersedia</span>
            <div class="cat-card-name">Semua</div>
            <div class="cat-card-count">{{ $menus->count() }} item</div>
            <i class="bi bi-grid cat-bg-icon"></i>
        </div>

        @php
            /**
             * Map nama kategori (lowercase) → style card + ikon Bootstrap Icons
             * Tambahkan entri baru jika ada kategori lain di database.
             *
             * Ikon yang dipakai:
             *   Makanan  → bi-egg-fried   (telur/makanan)
             *   Minuman  → bi-cup-straw   (gelas dengan sedotan)
             *   Snack    → bi-cookie      (biskuit)
             *   Kopi     → bi-cup-hot     (cangkir kopi)
             *   Dessert  → bi-cake2       (kue)
             *   default  → bi-box         (kotak generik)
             */
            $catMap = [
                'makanan'  => ['style' => 'cat-card-snack',  'icon' => 'bi-egg-fried'],
                'minuman'  => ['style' => 'cat-card-coffee', 'icon' => 'bi-cup-straw'],
                'snack'    => ['style' => 'cat-card-tea',    'icon' => 'bi-cookie'],
                'kopi'     => ['style' => 'cat-card-coffee', 'icon' => 'bi-cup-hot'],
                'dessert'  => ['style' => 'cat-card-snack',  'icon' => 'bi-cake2'],
                'desserts' => ['style' => 'cat-card-snack',  'icon' => 'bi-cake2'],
            ];

            // Fallback untuk kategori yang belum ada di $catMap
            $fallbackStyles = ['cat-card-tea', 'cat-card-coffee', 'cat-card-snack'];
            $fallbackIcons  = ['bi-grid', 'bi-cup-hot', 'bi-cookie'];
        @endphp

        @foreach($categories as $i => $cat)
        @php
            $key    = strtolower(trim($cat->name));
            $mapped = $catMap[$key] ?? null;
            $cStyle = $mapped ? $mapped['style'] : $fallbackStyles[$i % count($fallbackStyles)];
            $cIcon  = $mapped ? $mapped['icon']  : $fallbackIcons[$i % count($fallbackIcons)];
        @endphp
        <div class="cat-card {{ $cStyle }}"
             data-cat="{{ $cat->id }}"
             onclick="setCategory(this, '{{ $cat->id }}')">
            <span class="cat-status-badge badge-avail-green">Tersedia</span>
            <div class="cat-card-name">{{ $cat->name }}</div>
            <div class="cat-card-count">{{ $cat->menus_count }} item</div>
            <i class="bi {{ $cIcon }} cat-bg-icon"></i>
        </div>
        @endforeach

    </div>

    {{-- Menu Grid --}}
    <div class="kasir-menu-grid" id="menuGrid">
        @forelse($menus as $menu)
        <div class="menu-card"
             data-cat="{{ $menu->category }}"
             data-name="{{ strtolower($menu->name) }}">
            <div class="menu-card-img">
                @if($menu->image)
                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}">
                @else
                    ☕
                @endif
            </div>
            <div class="menu-card-name">{{ $menu->name }}</div>
            <div class="menu-card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
            <button class="menu-add-btn"
                    onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ $menu->image ? asset('storage/'.$menu->image) : '' }}')"
                    aria-label="Tambah {{ $menu->name }}">+</button>
        </div>
        @empty
        <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text3);">
            <i class="bi bi-cup" style="font-size:32px; display:block; margin-bottom:8px;"></i>
            Belum ada menu tersedia.
        </div>
        @endforelse
    </div>

</main>

{{-- ===== RECEIPT PANEL ===== --}}
<aside class="kasir-receipt">

    {{-- Tabs --}}
    <div class="receipt-tabs">
        <button class="receipt-tab active" onclick="setTab(this)">Makan di Sini</button>
        <button class="receipt-tab" onclick="setTab(this)">Bawa Pulang</button>
    </div>

    {{-- Customer & Table --}}
    <div class="receipt-fields">
        <div class="receipt-field">
            <label class="receipt-field-label" for="custName">Nama pelanggan</label>
            <input class="receipt-field-input" id="custName" type="text" placeholder="Nama...">
        </div>
        <div class="receipt-field" id="tableField">
            <label class="receipt-field-label" for="tableSelect">Meja</label>
            <select class="receipt-field-select" id="tableSelect">
                <option value="">— Pilih meja —</option>
                @foreach($mejas as $meja)
                @php $st = $mejaStatuses[$meja->no_meja] ?? 'kosong'; @endphp
                <option value="{{ $meja->no_meja }}" {{ $st !== 'kosong' ? 'disabled' : '' }}>
                    {{ $meja->no_meja }}{{ $st !== 'kosong' ? ' (terpakai)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Order List --}}
    <div class="receipt-order-list">
        <span class="receipt-order-label">Daftar Pesanan</span>
        <div id="cartItems"></div>
        <div class="receipt-empty" id="cartEmpty">
            <i class="bi bi-cart-x"></i>
            Belum ada pesanan.<br>Pilih menu di sebelah kiri.
        </div>
    </div>

    {{-- Footer --}}
    <div class="receipt-footer">
        <div class="receipt-total-row">
            <span>Subtotal</span>
            <span id="subtotalVal">Rp 0</span>
        </div>
        <div class="receipt-total-row">
            <span>Pajak (10%)</span>
            <span id="taxVal">Rp 0</span>
        </div>
        <div class="receipt-total-row grand">
            <span>Total</span>
            <span id="totalVal">Rp 0</span>
        </div>
        <button class="receipt-place-btn" onclick="placeOrder()">
            <i class="bi bi-arrow-right-circle"></i>
            Buat Pesanan
            <span class="receipt-place-price" id="placeTotal">Rp 0</span>
        </button>
    </div>

</aside>

@endsection

@push('custom_script')
<script>
var cart = [];

function fmt(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function addToCart(id, name, price, img) {
    var ex = cart.find(function(i) { return i.id === id; });
    if (ex) { ex.qty++; }
    else     { cart.push({ id: id, name: name, price: price, img: img, qty: 1 }); }
    renderCart();
}

function changeQty(id, delta) {
    var idx = cart.findIndex(function(i) { return i.id === id; });
    if (idx < 0) return;
    cart[idx].qty += delta;
    if (cart[idx].qty <= 0) cart.splice(idx, 1);
    renderCart();
}

function clearCart() {
    if (!cart.length) return;
    if (!confirm('Kosongkan semua pesanan?')) return;
    cart = [];
    renderCart();
}

function renderCart() {
    var wrap  = document.getElementById('cartItems');
    var empty = document.getElementById('cartEmpty');

    if (!cart.length) {
        wrap.innerHTML      = '';
        empty.style.display = '';
        updateTotals(0);
        return;
    }

    empty.style.display = 'none';
    wrap.innerHTML = cart.map(function(item) {
        var thumb = item.img
            ? '<img src="' + item.img + '" alt="' + item.name + '">'
            : '☕';
        return '<div class="order-item-row">' +
            '<div class="order-item-thumb">' + thumb + '</div>' +
            '<div class="order-item-body">' +
                '<div class="order-item-name">' + item.name + '</div>' +
                '<div class="order-item-mod">' + fmt(item.price) + ' × ' + item.qty + '</div>' +
                '<div class="order-item-qty-ctrl">' +
                    '<button class="qty-btn" onclick="changeQty(' + item.id + ', -1)">−</button>' +
                    '<span class="qty-num">' + item.qty + '</span>' +
                    '<button class="qty-btn" onclick="changeQty(' + item.id + ', 1)">+</button>' +
                '</div>' +
            '</div>' +
            '<div class="order-item-price">' + fmt(item.price * item.qty) + '</div>' +
        '</div>';
    }).join('');

    var sub = cart.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
    updateTotals(sub);
}

function updateTotals(sub) {
    var tax = Math.round(sub * 0.1);
    var tot = sub + tax;
    document.getElementById('subtotalVal').textContent = fmt(sub);
    document.getElementById('taxVal').textContent      = fmt(tax);
    document.getElementById('totalVal').textContent    = fmt(tot);
    document.getElementById('placeTotal').textContent  = fmt(tot);
}

function placeOrder() {
    if (!cart.length) { alert('Tambahkan menu terlebih dahulu.'); return; }

    var custName = document.getElementById('custName').value.trim();
    var table    = document.getElementById('tableSelect').value;
    var tab      = document.querySelector('.receipt-tab.active')?.textContent?.trim() || 'Makan di Sini';
    var sub      = cart.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
    var total    = sub + Math.round(sub * 0.1);

    if (!custName) { document.getElementById('custName').focus(); alert('Masukkan nama pelanggan.'); return; }
    if (tab === 'Makan di Sini' && !table) { alert('Pilih meja terlebih dahulu.'); return; }

    fetch('{{ route("kasir.orders.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'Accept'       : 'application/json',
            'X-CSRF-TOKEN' : '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            customer_name : custName,
            table_number  : table,
            order_type    : tab,
            items         : cart.map(function(i) { return { menu_id: i.id, quantity: i.qty, price: i.price }; }),
            total         : total
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            window.location.href = '/kasir/pembayaran/' + data.order_id;
        } else {
            alert(data.message || 'Gagal membuat pesanan.');
        }
    })
    .catch(function() { alert('Terjadi kesalahan. Coba lagi.'); });
}

var activeCategory = 'semua';

function setCategory(el, catId) {
    document.querySelectorAll('.cat-card').forEach(function(c) { c.classList.remove('active'); });
    el.classList.add('active');
    activeCategory = String(catId);
    applyFilter();
}

function filterMenu(query) { applyFilter(query); }

function applyFilter(query) {
    var q = (query !== undefined ? query : document.getElementById('menuSearch').value).toLowerCase();
    document.querySelectorAll('.menu-card').forEach(function(card) {
        var matchCat  = activeCategory === 'semua' || card.dataset.cat === activeCategory;
        var matchName = !q || card.dataset.name.includes(q);
        card.style.display = (matchCat && matchName) ? '' : 'none';
    });
}

function setTab(btn) {
    document.querySelectorAll('.receipt-tab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var tab = btn.textContent.trim();
    var tableField = document.getElementById('tableField');
    if (tab === 'Bawa Pulang') {
        tableField.style.display = 'none';
        document.getElementById('tableSelect').value = '';
    } else {
        tableField.style.display = '';
    }
}

document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('menuSearch').focus();
    }
});
</script>
@endpush