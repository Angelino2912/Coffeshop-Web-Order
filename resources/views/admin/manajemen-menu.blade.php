@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/admin/manajemen-menu.css') }}">
@endpush

@section('title', 'Manajemen Menu')

@section('content')
<div class="mm-wrapper">

    <div class="mm-header">
        <h1>Manajemen Menu</h1>
        <button class="btn-tambah" onclick="document.getElementById('modalTambah').style.display='flex'">
            + Tambah Menu
        </button>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="mm-table-wrapper">
        <table class="mm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <span class="badge
                            @if($item->category == 'Makanan') badge-makanan
                            @elseif($item->category == 'Minuman') badge-minuman
                            @else badge-snack
                            @endif">
                            {{ $item->category }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="aksi-col">
                        <button class="btn-edit"
                            onclick="openEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->category }}', {{ $item->price }})">
                            Edit
                        </button>
                        <button class="btn-hapus"
                            onclick="openHapus({{ $item->id }}, '{{ addslashes($item->name) }}')">
                            Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty">Belum ada menu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal-overlay" id="modalTambah">
        <div class="modal-box">
            <h2>Tambah Menu</h2>
            <form action="/admin/manajemen-menu" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="name" required placeholder="Contoh: Nasi Goreng">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" required>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Snack">Snack</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="price" required placeholder="Contoh: 25000">
                </div>
                <div class="form-group">
                    <label>Foto Menu <small style="color:#9ca3af">(opsional)</small></label>
                    <input type="file" name="image" accept="image/*" onchange="previewFoto(this, 'previewTambah')">
                    <img id="previewTambah" src="" alt=""
                         style="display:none;margin-top:8px;width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-batal"
                        onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
                    <button type="submit" class="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal-overlay" id="modalEdit">
        <div class="modal-box">
            <h2>Edit Menu</h2>
            <form action="" method="POST" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" id="editCategory" required>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Snack">Snack</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="price" id="editPrice" required>
                </div>
                <div class="form-group">
                    <label>Foto Menu <small style="color:#9ca3af">(kosongkan jika tidak diganti)</small></label>
                    <input type="file" name="image" accept="image/*" onchange="previewFoto(this, 'previewEdit')">
                    <img id="previewEdit" src="" alt=""
                         style="display:none;margin-top:8px;width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-batal"
                        onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
                    <button type="submit" class="btn-simpan">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal-overlay" id="modalHapus">
        <div class="modal-box modal-box-sm">
            <div class="hapus-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="12" fill="#fee2e2"/>
                    <path d="M9 9l6 6M15 9l-6 6" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="hapus-title">Hapus Menu?</h2>
            <p class="hapus-desc">Menu "<span id="hapusNama" style="font-weight:700;"></span>" akan dihapus secara permanen.</p>
            <form action="" method="POST" id="formHapus">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="button" class="btn-batal"
                        onclick="document.getElementById('modalHapus').style.display='none'">Batal</button>
                    <button type="submit" class="btn-hapus-confirm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function openEdit(id, name, category, price) {
    document.getElementById('editName').value = name;
    document.getElementById('editCategory').value = category;
    document.getElementById('editPrice').value = price;
    document.getElementById('formEdit').action = '/admin/manajemen-menu/' + id;
    document.getElementById('previewEdit').style.display = 'none';
    document.getElementById('modalEdit').style.display = 'flex';
}

function openHapus(id, name) {
    document.getElementById('hapusNama').textContent = name;
    document.getElementById('formHapus').action = '/admin/manajemen-menu/' + id;
    document.getElementById('modalHapus').style.display = 'flex';
}

function previewFoto(input, previewId) {
    var img = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.style.display = 'none';
        }
    });
});
</script>
@endsection