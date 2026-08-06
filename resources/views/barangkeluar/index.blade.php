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
    .filter-form button:hover {
        background-color: #2563eb;
    }


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


    /* Tombol Aksi / Detail */
    .td-action { display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: nowrap; }
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
            text-align: left;                /* label rata kiri */
            padding: 10px 15px 10px 60%;
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


        /* Label per kolom (urut sesuai <th>) */
        td:nth-of-type(1):before  { content: "No"; }
        td:nth-of-type(2):before  { content: "Kode Barang"; }
        td:nth-of-type(3):before  { content: "Nama Barang"; }
        td:nth-of-type(4):before  { content: "Lokasi"; }
        td:nth-of-type(5):before  { content: "Kondisi"; }
        td:nth-of-type(6):before  { content: "Jumlah Keluar"; }
        td:nth-of-type(7):before  { content: "Harga Jual / Unit"; }
        td:nth-of-type(8):before  { content: "Total Harga"; }
        td:nth-of-type(9):before  { content: "Catatan"; }
        td:nth-of-type(10):before { content: "Penerima"; }
        td:nth-of-type(11):before { content: "User"; }
        td:nth-of-type(12):before { content: "Lokasi Tujuan"; }
        td:nth-of-type(13):before { content: "Tanggal Keluar"; }
        td:nth-of-type(14):before { content: "Detail"; }


        


        /* Aksi/Detail tetap rapi */
        .td-action { justify-content: flex-start; }
    }
</style>


<div class="container-laporan">
    <div class="header">
        <h4>Daftar Barang Keluar</h4>
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary">+ Tambah Barang Keluar</a>
    </div>


    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif


    {{-- Filter/Search --}}
    <form action="{{ route('barang-keluar.index') }}" method="GET" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input
                type="text"
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="Cari Kode Barang, Nama Barang, Penerima, Lokasi"
            >
        </div>


        <div class="form-group">
            <label for="lokasi">Lokasi</label>
            <select name="lokasi" id="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                        {{ $lokasi->nama_lokasi }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group" style="flex-direction: row; gap: 10px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>


    <div class="mb-3">
        <strong>Total Data:</strong> {{ $barangKeluars->total() }}
    </div>


    {{-- Tabel/Kartu --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Jumlah Keluar</th>
                    <th>Harga Jual / Unit</th>
                    <th>Total Harga</th>
                    <th>Catatan</th>
                    <th>Penerima</th>
                    <th>User</th>
                    <th>Lokasi Tujuan</th>
                    <th>Tanggal Keluar</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangKeluars as $index => $keluar)
                    <tr>
                        <td>{{ $barangKeluars->firstItem() + $index }}</td>
                        <td>{{ $keluar->kode_barang }}</td>
                        <td>{{ $keluar->item->nama_barang ?? '-' }}</td>
                        <td>{{ $keluar->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>{{ $keluar->kondisi->nama_kondisi ?? '-' }}</td>


                        {{-- Kolom 6–8 dibungkus dengan .v agar nilai rata kanan di mobile --}}
                        <td><span class="v">{{ $keluar->jumlah_keluar }}</span></td>
                        <td><span class="v">Rp{{ number_format($keluar->harga_jual, 0, ',', '.') }}</span></td>
                        <td><span class="v">Rp{{ number_format($keluar->total_harga_jual, 0, ',', '.') }}</span></td>


                        <td>{{ $keluar->catatan ?? '-' }}</td>
                        <td>{{ $keluar->penerima ?? '-' }}</td>
                        <td>{{ $keluar->user->username ?? '-' }}</td>
                        <td>{{ $keluar->lokasi_tujuan ?? '-' }}</td>
                        <td>{{ $keluar->tanggal_keluar ? \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y H:i:s') : '-' }}</td>

                        <td class="text-start">
                            <a href="{{ route('barang-keluar.detail', $keluar->id) }}" class="btn btn-sm btn-info btn-action">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="14" class="text-center">Tidak ada data barang keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{-- Pagination --}}
    <div style="margin-top:20px; text-align:center;">
        {{ $barangKeluars->appends(request()->query())->links() }}
    </div>


    @if ($barangKeluars->total() > 0)
        <p class="text-muted">
            {{ $barangKeluars->total() }} hasil ditemukan.
            Halaman {{ $barangKeluars->currentPage() }} dari {{ $barangKeluars->lastPage() }}.
        </p>
    @else
        <p class="text-danger">Tidak ada hasil yang ditemukan.</p>
    @endif
</div>
@endsection
