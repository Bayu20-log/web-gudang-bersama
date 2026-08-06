@extends('layouts.app')

@section('content')
<style>
    /* Hindari nempel ke navbar */
    body { padding-top: 40px; }

    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ==== HEADER ==== */
    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .back-button, .btn-secondary, .create-button {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        border: none;
        outline: none;
        display: inline-block;
    }
    .back-button { background-color: #e5e7eb; color: #111827; }
    .back-button:hover { background-color: #d1d5db; }
    .create-button { background-color: #1a5de2; color: #fff; }

    /* ==== FILTER ==== */
    form.filter-form {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }
    .filter-form .form-group {
        display: flex;
        flex-direction: column;
        min-width: 200px;
    }
    .filter-form label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .filter-form input,
    .filter-form select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    .filter-form button {
        height: 38px;
        padding: 0 16px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .filter-form button:hover { background-color: #2563eb; }

    /* ==== TABLE ==== */
    .table-wrapper { width: 100%; overflow-x: auto; }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    th, td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
    }
    th {
        background-color: #f3f4f6;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Tombol Aksi */
    .td-action {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
    }
    .btn-action {
        padding: 6px 10px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    /* ==== MOBILE: jadikan kartu ==== */
    @media(max-width: 768px) {
        .header { flex-direction: column; align-items: flex-start; }
        .filter-form { flex-direction: column; }
        .filter-form .form-group { width: 100%; }
        .filter-form button, .btn-secondary { width: 100%; }

        table, thead, tbody, th, td, tr { display: block; width: 100%; }
        thead { display: none; }

        tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        td {
            border: none !important;
            text-align: left;
            padding: 8px 12px 8px 55%;
            position: relative;
        }
        td:before {
            position: absolute;
            top: 8px;
            left: 12px;
            width: 38%;
            white-space: nowrap;
            font-weight: bold;
            color: #333;
            content: "";
        }

        /* Label sesuai urutan kolom */
        td:nth-of-type(1):before { content: "Kode Barang"; }
        td:nth-of-type(2):before { content: "Nama Barang"; }
        td:nth-of-type(3):before { content: "Kategori"; }
        td:nth-of-type(4):before { content: "Satuan"; }
        td:nth-of-type(5):before { content: "Stok Minimum"; }
        td:nth-of-type(6):before { content: "Foto"; }
        td:nth-of-type(7):before { content: "Aksi"; }

        .td-action { justify-content: flex-start; }
    }
</style>

<div class="container-laporan">
    <div class="header">
        <h4>Daftar Item</h4>
        <a href="{{ route('item.create') }}" class="create-button">+ Tambah Item</a>
    </div>

    {{-- Form Filter --}}
    <form method="GET" action="{{ route('item.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input type="text" id="search" name="search" placeholder="Cari nama barang" value="{{ request('search') }}">
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

        <div class="form-group" style="flex-direction: row; gap: 10px;">
            <button type="submit">Filter</button>
            <a href="{{ route('item.index') }}" class="back-button">Reset</a>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Stok Minimum</th>
                    <th>Foto</th>
                    <th >Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->kategori->kategori }}</td>
                        <td>{{ $item->satuan->nama_satuan ?? '-' }}</td>
                        <td>{{ $item->stok_minimum }}</td>
                        <td>
                            @if ($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" 
                                     alt="Foto {{ $item->nama_barang }}" 
                                     class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                            @else
                                <span class="text-muted fst-italic">(Tidak ada foto)</span>
                            @endif
                        </td>
                        <td class="text-start">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('item.show', $item->kode_barang) }}" 
                                class="btn-action" style="background-color: #60a5fa; color: #fff;">Lihat</a>
                                <a href="{{ route('item.edit', $item->kode_barang) }}" 
                                class="btn-action" style="background-color: #fbbf24; color: #111;">Edit</a>
                                <form action="{{ route('item.destroy', $item->kode_barang) }}" 
                                    method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action" style="background-color: #ef4444; color: #fff;">Hapus</button>
                                </form>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px; text-align:center;">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>


@endsection
