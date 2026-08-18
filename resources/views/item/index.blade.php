@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 1px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; }
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }
    .bg-dark-header { background-color: #1e293b !important; color: #fff; border-bottom: 2px solid #f97316; }

    .container-laporan { max-width: 1200px; margin: auto; padding: 30px 20px; font-family: 'Segoe UI', sans-serif; }
    .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }

    form.filter-form { background-color: #ffffff; border: none; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 250px; flex-grow: 1; }
    .filter-form label { font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;}
    .filter-form input, .filter-form select { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: 0.3s; }
    .filter-form input:focus, .filter-form select:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
    
    .table-wrapper { width: 100%; overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }
    tr:hover { background-color: #f8fafc; }

    .btn-action { padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap; border: none; transition: 0.2s; }
    .btn-action:hover { opacity: 0.85; transform: translateY(-2px); }

    .img-thumbnail-custom { width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; cursor: pointer; transition: 0.3s; background: #fff;}
    .img-thumbnail-custom:hover { transform: scale(1.15); border-color: #f97316; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2); }

    @media(max-width: 768px) {
        .header { flex-direction: column; align-items: flex-start; }
        .filter-form { flex-direction: column; }
        .filter-form .form-group, .btn-action-group { width: 100%; }
        .btn-action-group { display: flex; gap: 10px; }
        .btn-action-group button, .btn-action-group a { flex: 1; text-align: center; justify-content: center; }
        
        table, thead, tbody, th, td, tr { display: block; width: 100%; }
        thead { display: none; }
        tr { margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; background-color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        td { border: none !important; text-align: left; padding: 8px 0 8px 45%; position: relative; }
        td:before { position: absolute; top: 8px; left: 0; width: 40%; white-space: nowrap; font-weight: 600; color: #4b5563; }
        
        td:nth-of-type(1):before { content: "Kode Produk"; }
        td:nth-of-type(2):before { content: "Nama Produk"; }
        td:nth-of-type(3):before { content: "Kategori"; }
        td:nth-of-type(4):before { content: "Satuan"; }
        td:nth-of-type(5):before { content: "Stok Min"; }
        td:nth-of-type(6):before { content: "Foto"; }
        td:nth-of-type(7):before { content: "Aksi"; }
        .td-action { justify-content: flex-start; flex-wrap: wrap; gap: 8px;}
    }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Katalog &rsaquo; Daftar Produk</h4>
        <a href="{{ route('item.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Produk</a>
    </div>

    <form method="GET" action="{{ route('item.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Produk</label>
            <input type="text" id="search" name="search" placeholder="Ketik nama atau kode produk..." value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori">
                <option value="">-- Semua Kategori --</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="btn-action-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-orange fw-bold px-4">Filter</button>
            <a href="{{ route('item.index') }}" class="btn btn-outline-dark fw-bold px-4" style="display: inline-flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Stok Min.</th>
                    <th>Foto</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="fw-medium text-secondary">{{ $item->kode_barang }}</td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $item->nama_barang }}</td>
                        <td>{{ $item->kategori->kategori }}</td>
                        <td>{{ $item->satuan->nama_satuan ?? '-' }}</td>
                        <td><span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $item->stok_minimum }}</span></td>
                        <td>
                            @if ($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="img-thumbnail-custom shadow-sm"
                                      onclick="openLightbox('{{ asset('storage/' . $item->foto) }}', '{{ $item->nama_barang }}')">
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">Kosong</span>
                            @endif
                        </td>
                        <td class="td-action">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('item.show', $item->kode_barang) }}" class="btn-action text-white shadow-sm" style="background-color: #3b82f6;">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                                <a href="{{ route('item.edit', $item->kode_barang) }}" class="btn-action text-dark shadow-sm" style="background-color: #facc15;">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <form id="delete-form-{{ $item->kode_barang }}" action="{{ route('item.destroy', $item->kode_barang) }}" method="POST" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action text-white shadow-sm" style="background-color: #ef4444;" onclick="confirmDelete('{{ $item->kode_barang }}')">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted fw-medium"><i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-light"></i>Data produk belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- MODAL LIGHTBOX FOTO -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold" id="lightboxTitle">Preview Foto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center" style="background-color: #f8fafc;">
         <img id="lightboxImage" src="" class="img-fluid rounded shadow" style="max-height: 65vh; object-fit: contain;">
      </div>
    </div>
  </div>
</div>

<script>
    function openLightbox(imageUrl, title) {
        document.getElementById('lightboxImage').src = imageUrl;
        document.getElementById('lightboxTitle').innerText = title;
        var myModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
        myModal.show();
    }
    function confirmDelete(kodeBarang) {
        Swal.fire({
            title: 'Hapus Produk?',
            text: "Data produk ini akan dihapus secara permanen dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#1e293b',
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + kodeBarang).submit();
            }
        });
    }
</script>
@endsection