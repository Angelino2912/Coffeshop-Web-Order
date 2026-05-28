@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/reviews.css') }}">
@endpush

@section('content')
<div class="reviews-page">
    <div class="page-header">
        <h1>Reviews</h1>
        <p>Ulasan pelanggan setelah pesanan diantarkan.</p>
    </div>

    <div class="review-list">
        @forelse($reviews as $review)
            <div class="review-card">
                <div class="review-top">
                    <div>
                        <h2>{{ $review->customer_name }}</h2>
                        <p>Meja {{ $review->order?->table_number ?? '-' }} &bullet; {{ $review->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="rating">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $review->rating ? 'active' : '' }}">&#9733;</span>
                        @endfor
                    </div>
                </div>

                <p class="comment">{{ $review->comment ?: 'Tidak ada komentar.' }}</p>

                <div class="order-summary">
                    <strong>Pesanan:</strong>
                    @if($review->order && $review->order->items->count())
                        {{ $review->order->items->map(fn($item) => ($item->menu?->name ?? 'Menu tidak tersedia') . ' x' . $item->quantity)->join(', ') }}
                    @else
                        -
                    @endif
                </div>
            </div>
        @empty
            <div class="empty">Belum ada review.</div>
        @endforelse
    </div>
</div>
@endsection
