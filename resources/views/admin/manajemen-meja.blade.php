@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/dashboard.css') }}">
@endpush

@section('title', 'Manajemen Meja')

@section('content')
<div class="header">
    <h1>Manajemen Meja</h1>
    <p>Kelola jumlah meja yang tersedia untuk customer.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-label">Total meja</div>
        <div class="stat-value">{{ $mejas->count() }}</div>
    </div>
</div>

<div class="meja-status-section">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px;">
        <h2 style="margin:0; font-size:16px; font-weight:700; color:#3d2b1f;">Daftar Meja</h2>
        <button data-bs-toggle="modal" data-bs-target="#modalTambahMeja"
            style="background:#9b632c; color:#fff; border:none; border-radius:10px; padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
            <i class="bi bi-plus-circle"></i> Tambah Meja
        </button>
    </div>

    <div class="meja-status-grid">
        @forelse($mejas as $meja)
            @php
                $activeOrder = \App\Models\Order::where('table_number', $meja->no_meja)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->latest()
                    ->first();
                $mejaStatus = $activeOrder ? ($activeOrder->status === 'pending' ? 'pending' : 'aktif') : 'kosong';
            @endphp
            <div class="meja-status-box meja-box-{{ $mejaStatus }}" data-meja="{{ $meja->no_meja }}">
                <div class="meja-status-no">{{ $meja->no_meja }}</div>
                <div class="meja-status-label">
                    <span class="status-dot"></span>
                    <span class="status-text">
                        @if($mejaStatus === 'pending') Memesan
                        @elseif($mejaStatus === 'aktif') Diproses
                        @else Kosong
                        @endif
                    </span>
                </div>
                <div class="meja-cust-name" @if($mejaStatus === 'kosong') style="display:none" @endif>
                    {{ $activeOrder?->customer_name ?? '' }}
                </div>
                <button class="btn-delete-meja"
                    onclick="deleteMeja('{{ $meja->no_meja }}', '{{ $mejaStatus }}')"
                    {{ $mejaStatus !== 'kosong' ? 'disabled' : '' }}
                    title="{{ $mejaStatus !== 'kosong' ? 'Meja sedang digunakan' : 'Hapus meja ini' }}">
                    <i class="bi bi-trash"></i>
                    {{ $mejaStatus !== 'kosong' ? 'Sedang digunakan' : 'Hapus Meja' }}
                </button>
            </div>
        @empty
            <div class="order-card"><p>Belum ada meja.</p></div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="modalTambahMeja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:8px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Meja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST" action="/admin/meja/store">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Meja Awal</label>
                        <input type="number" name="no_meja_awal" class="form-control" min="1" value="1" required
                               style="border-radius:10px; padding:10px 14px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Meja</label>
                        <input type="number" name="jumlah_meja" class="form-control" min="1" max="100" value="10" required
                               style="border-radius:10px; padding:10px 14px;">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                                style="border-radius:10px;">Batal</button>
                        <button type="submit" class="btn px-4 text-white"
                                style="background:#9b632c; border-radius:10px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px; padding:8px; text-align:center;">
            <div class="modal-body pt-4 pb-2">
                <div style="font-size:40px; color:#e74c3c; margin-bottom:12px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold" style="color:#3d2b1f; margin-bottom:8px;">Hapus Meja?</h5>
                <p style="color:#8a6a54; font-size:13px; margin-bottom:20px;">
                    Meja <strong id="konfirmasi-meja-no"></strong> akan dihapus permanen.
                </p>
                <div class="d-flex gap-2 justify-content-center mb-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                            style="border-radius:10px; font-size:13px;">Batal</button>
                    <button type="button" id="btn-konfirmasi-hapus" class="btn px-4 text-white"
                            style="background:#e74c3c; border-radius:10px; font-size:13px;">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom_script')
<script>
var mejaToDelete = null;

function deleteMeja(noMeja, status) {
    if (status !== 'kosong') {
        alert('Meja sedang digunakan, tidak bisa dihapus.');
        return;
    }

    mejaToDelete = noMeja;
    document.getElementById('konfirmasi-meja-no').textContent = noMeja;
    new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus')).show();
}

document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function () {
    if (!mejaToDelete) return;

    var btn = this;
    var noMeja = mejaToDelete;
    btn.disabled = true;

    fetch('/admin/meja/' + encodeURIComponent(noMeja) + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalKonfirmasiHapus'))?.hide();

        if (data.success) {
            var box = document.querySelector('[data-meja="' + noMeja + '"]');
            if (box) box.remove();
        } else {
            alert(data.message || 'Gagal menghapus meja.');
        }
    })
    .catch(() => alert('Terjadi kesalahan koneksi.'))
    .finally(() => {
        btn.disabled = false;
        mejaToDelete = null;
    });
});
</script>
@endpush