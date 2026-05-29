@extends('template.customer')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/menu.css') }}">
@endpush

@section('content')

<div class="hero-bg"></div>

<div class="menu-layout">
    <aside class="sidebar-nav">
        <div class="brand">
            <h1>Cafe</h1>
            <p>Cinta Rasa</p>
        </div>

        <div class="table-pill">Meja {{ session('no_meja') }}</div>

        <nav>
            <a href="/menu" class="active"><i class="bi bi-grid"></i> Menu</a>
            <a href="/cart"><i class="bi bi-bag"></i> Keranjang</a>
            <a href="/checkout"><i class="bi bi-receipt"></i> Checkout</a>
        </nav>
    </aside>

    <div class="container">

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <!-- Search & Filter Wrapper -->
    <div class="search-filter-wrap">
        <div class="search-box-container">
            <input type="text" id="menuSearch" placeholder="Cari makanan atau minuman...">
            <i class="bi bi-search"></i>
        </div>
        <div class="category-filters">
            <button type="button" class="filter-btn active" data-category="Makanan">Makanan</button>
            <button type="button" class="filter-btn" data-category="Minuman">Minuman</button>
        </div>
    </div>

    {{-- MAKANAN --}}
    <div class="kelompok" data-group-category="Makanan">
        <h2>Makanan</h2>
        <details class="order-guide">
            <summary>
                <span>Cara Pesan</span>
                <strong>Baca selengkapnya</strong>
            </summary>
            <ol>
                <li>Pilih menu makanan atau minuman.</li>
                <li>Tambahkan item ke keranjang.</li>
                <li>Periksa keranjang lalu checkout.</li>
                <li>Tambahkan catatan jika perlu, kemudian konfirmasi pesanan.</li>
            </ol>
        </details>
        <div class="menu">
            @foreach($items as $item)
                @if($item->category == 'Makanan')
                    <div class="menu-item" data-name="{{ strtolower($item->name) }}">
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
    <div class="kelompok" data-group-category="Minuman">
        <h2>Minuman</h2>
        <div class="menu">
            @foreach($items as $item)
                @if($item->category == 'Minuman')
                    <div class="menu-item" data-name="{{ strtolower($item->name) }}">
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
</div>
@endsection

@push('custom_script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menuSearch');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const categories = document.querySelectorAll('.kelompok');

    function filterMenu() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const activeCategory = document.querySelector('.filter-btn.active').dataset.category;

        categories.forEach(category => {
            const categoryName = category.dataset.groupCategory;
            let hasVisibleItems = false;

            const itemsInGroup = category.querySelectorAll('.menu-item');
            itemsInGroup.forEach(item => {
                const itemName = item.dataset.name;
                
                // Check category match
                const categoryMatch = (activeCategory === 'all' || categoryName === activeCategory);
                
                // Check search match
                const searchMatch = itemName.includes(searchTerm);

                if (categoryMatch && searchMatch) {
                    item.style.display = '';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show or hide the whole category section
            if (hasVisibleItems) {
                category.style.display = '';
            } else {
                category.style.display = 'none';
            }
        });
    }

    // Search Input Event Listener
    searchInput.addEventListener('input', filterMenu);

    // Filter Buttons Event Listener
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterMenu();
        });
    });

    filterMenu();
});
</script>
@endpush
