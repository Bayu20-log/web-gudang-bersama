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

    .filter-form button,
    .filter-form .btn-secondary {
        height: 38px;
        font-size: 14px;
        font-weight: 500;
    }

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

    /* Mobile Responsive */
   @media(max-width: 768px) {
    /* Header & Filter */
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


    /* Label Kolom */
    td:nth-of-type(1):before { content: "No"; }
    td:nth-of-type(2):before { content: "Nama Pemasok"; }
    td:nth-of-type(3):before { content: "Email"; }
    td:nth-of-type(4):before { content: "Jenis"; }
    td:nth-of-type(5):before { content: "Alamat"; }
    td:nth-of-type(6):before { content: "Nomor Telepon"; }
    td:nth-of-type(7):before { content: "Nama PIC"; }
    td:nth-of-type(8):before { content: "Bergabung Sejak"; }
    td:nth-of-type(9):before { content: "Update Terakhir"; }
    td:nth-of-type(10):before { content: "Aksi"; }

    /* Tombol Aksi di Card */
    .td-action {
        flex-direction: column;
        align-items: stretch;
        gap: 5px;
    }
    .td-action a, .td-action button {
        width: 100%;
    }
}

</style>

<div class="container-laporan">
    <div class="header">
        <h4>Daftar Pemasok</h4>
        <a href="{{ route('pemasok.create') }}" class="btn btn-primary">+ Tambah Pemasok</a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('pemasok.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari:</label>
            <input type="text" name="search" id="search" placeholder="Nama, jenis, alamat, email, PIC" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('pemasok.index') }}" class="btn btn-secondary">Reset</a>

    </form>
    
    {{-- Tabel --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pemasok</th>
                    <th>Email</th>
                    <th>Jenis</th>
                    <th>Alamat</th>
                    <th>Nomor Telepon</th>
                    <th>Nama PIC</th>
                    <th>Bergabung Sejak</th>
                    <th>Update Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemasoks as $pemasok)
                    <tr>
                        <td>{{ ($pemasoks->currentPage() - 1) * $pemasoks->perPage() + $loop->iteration }}</td>
                        <td>{{ $pemasok->nama_pemasok }}</td>
                        <td>{{ $pemasok->email ?? '-' }}</td>
                        <td>{{ $pemasok->jenis ?? '-' }}</td>
                        <td>{{ $pemasok->alamat ?? '-' }}</td>
                        <td>{{ $pemasok->no_telepon ?? '-' }}</td>
                        <td>{{ $pemasok->nama_pic ?? '-' }}</td>
                        <td>
                            {{ $pemasok->bergabung_sejak 
                                ? \Carbon\Carbon::parse($pemasok->bergabung_sejak)->format('d-m-Y') 
                                : '-' }}
                        </td>
                        <td>{{ optional($pemasok->updated_at)->format('d-m-Y H:i:s') ?? '-' }}</td>
                        <td>
    <div style="display: flex; gap: 5px;">
        <a href="{{ route('pemasok.edit', $pemasok->id) }}" class="btn-action btn-edit">Edit</a>
        <form action="{{ route('pemasok.destroy', $pemasok->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action btn-delete">Hapus</button>
        </form>
    </div>
</td>

                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px;text-align;center;">
        {{ $pemasoks->links() }}
    </div>
</div>
@endsection
