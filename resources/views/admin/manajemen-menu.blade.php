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
            <form action="/admin/manajemen-menu" method="POST">
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
            <form action="" method="POST" id="formEdit">
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
    document.getElementById('modalEdit').style.display = 'flex';
}
</script>
@endsection