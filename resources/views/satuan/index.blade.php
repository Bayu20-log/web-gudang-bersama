@extends('layouts.app')

@section('content')
<style>
    body {
        padding-top: 40px; /* Sesuaikan dengan tinggi navbar */
    }
    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .back-button, .btn-secondary {
        padding: 8px 14px;
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        border: none;
        outline: none;
    }
    .back-button:hover, .btn-secondary:hover {
        background-color: #d1d5db;
    }

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
    .filter-form input {
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
    .filter-form button:hover {
        background-color: #2563eb;
    }

    /* Table */
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
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
    }

    /* Tombol Aksi */
    .td-action {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
    }
    .td-action form {
        display: inline;
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
        gap: 5px;
        white-space: nowrap;
    }
    .btn-edit { background-color: #facc15; color: #1f2937; }
    .btn-delete { background-color: #ef4444; color: white; }
    .btn-edit:hover { background-color: #eab308; }
    .btn-delete:hover { background-color: #dc2626; }
    /* Default tombol aksi */
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap; /* ✅ biar kalau sempit turun baris */
    }

    /* Mobile */
    @media(max-width: 768px) {
        .action-buttons {
            justify-content: flex-start; /* ✅ geser ke kiri */
            flex-wrap: nowrap;           /* ✅ biar tetap sejajar */
            overflow-x: auto;            /* ✅ kalau kepanjangan bisa di-scroll horizontal */
        }

        .action-buttons a,
        .action-buttons button {
            flex-shrink: 0; /* ✅ biar tidak kepotong */
        }
    }

    /* Mobile Responsive */
    @media(max-width: 768px) {
        .header { 
            flex-direction: column; 
            align-items: flex-start; 
        }
        .filter-form { 
            flex-direction: column; 
        }
        .filter-form .form-group { 
            width: 100%; 
        }
        .filter-form button, .btn-secondary { 
            width: 100%; 
        }
            /* Table jadi Card View */
    table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
    }
    thead { 
        display: none; 
    }

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
        padding: 8px 12px 8px 45%; /* ✅ kasih ruang lebih */
        position: relative;
        word-wrap: break-word;     /* ✅ biar isi text turun ke bawah kalau panjang */
        white-space: normal;       /* ✅ teks bisa wrap */
    }

    td:before {
        position: absolute;
        top: 8px;
        left: 12px;
        width: 40%;                /* ✅ label lebih lebar */
        font-weight: bold;
        color: #333;
        white-space: normal;       /* ✅ biar label juga bisa turun ke bawah */
    }
        td:nth-of-type(1):before { content: "No"; }
        td:nth-of-type(2):before { content: "Nama Satuan"; }
     
        td:nth-of-type(3):before { content: "Created At"; }
        td:nth-of-type(4):before { content: "Updated At"; }
        td:nth-of-type(5):before { content: "Aksi"; }

        /* Tombol Aksi tetap sejajar di mobile */
        .td-action {
            flex-direction: row;
            justify-content: flex-start; /* geser ke kiri */
            align-items: center;
            gap: 6px;
        }

        .td-action a,
        .td-action button {
            width: auto;
        }
    }
</style>

<div class="container-laporan">
    <div class="header">
        <h4>Daftar Satuan</h4>
        <a href="{{ route('satuan.create') }}" class="btn btn-primary">+ Tambah Satuan</a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('satuan.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari:</label>
            <input type="text" name="search" id="search" placeholder="Cari nama satuan" value="{{ request('search') }}">
        </div>
        <button type="submit">Filter</button>
        <a href="{{ route('satuan.index') }}" class="back-button">Reset</a>
    </form>

    {{-- Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Satuan</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($satuans as $index => $satuan)
                    <tr>
                        <td>{{ $satuans->firstItem() + $index }}</td>
                        <td>{{ $satuan->nama_satuan }}</td>
                        <td>{{ optional($satuan->created_at)->format('d-m-Y H:i:s') }}</td>
                        <td>{{ optional($satuan->updated_at)->format('d-m-Y H:i:s') }}</td>
                        <td>
                            
                            <a href="{{ route('satuan.edit', $satuan->id) }}" class="btn-action btn-edit">Edit</a>
                            <form action="{{ route('satuan.destroy', $satuan->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin ingin menghapus?')" class="btn-action btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px; text-align:center;">
        {{ $satuans->links() }}
    </div>

    {{-- Info jumlah data --}}
    @if ($satuans->total() > 0)
        <p class="text-muted mt-2">
            {{ $satuans->total() }} hasil ditemukan.
            Halaman {{ $satuans->currentPage() }} dari {{ $satuans->lastPage() }}.
        </p>
    @else
        <p class="text-danger mt-2">Tidak ada hasil yang ditemukan.</p>
    @endif
</div>
@endsection
