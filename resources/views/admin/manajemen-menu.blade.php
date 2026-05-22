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
                    <td><span class="badge {{ $item->category == 'Makanan' ? 'badge-makanan' : 'badge-minuman' }}">{{ $item->category }}</span></td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="aksi-col">
                        <button class="btn-edit" onclick="openEdit({{ $item->id }}, '{{ $item->name }}', '{{ $item->category }}', {{ $item->price }})">Edit</button>
                        <form action="/admin/manajemen-menu/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty">Belum ada menu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="modalTambah">
        <div class="modal-box">
            <h2>Tambah Menu</h2>
            {{-- enctype wajib untuk upload file --}}
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
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="price" required placeholder="Contoh: 25000">
                </div>
                {{-- INPUT FOTO --}}
                <div class="form-group">
                    <label>Foto Menu <small style="color:#9ca3af">(opsional)</small></label>
                    <input type="file" name="image" accept="image/*" onchange="previewFoto(this, 'previewTambah')">
                    <img id="previewTambah" src="" alt="" style="display:none;margin-top:8px;width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-batal" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
                    <button type="submit" class="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalEdit">
        <div class="modal-box">
            <h2>Edit Menu</h2>
            {{-- enctype wajib untuk upload file --}}
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
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="price" id="editPrice" required>
                </div>
                {{-- INPUT FOTO --}}
                <div class="form-group">
                    <label>Foto Menu <small style="color:#9ca3af">(kosongkan jika tidak diganti)</small></label>
                    <input type="file" name="image" accept="image/*" onchange="previewFoto(this, 'previewEdit')">
                    <img id="previewEdit" src="" alt="" style="display:none;margin-top:8px;width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-batal" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
                    <button type="submit" class="btn-simpan">Update</button>
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
    // reset preview foto saat modal dibuka
    document.getElementById('previewEdit').style.display = 'none';
    document.getElementById('modalEdit').style.display = 'flex';
}

function previewFoto(input, previewId) {
    const img = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection