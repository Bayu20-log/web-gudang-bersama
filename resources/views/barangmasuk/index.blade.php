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
        td:nth-of-type(4):before  { content: "Jumalah"; }
        td:nth-of-type(5):before  { content: "Harga Beli"; }
        td:nth-of-type(6):before  { content: "Total Harga"; }
        td:nth-of-type(7):before  { content: "TanggAL Masuk"; }
        td:nth-of-type(8):before  { content: "Tanggal Kadaluarsa"; }
        td:nth-of-type(9):before  { content: "Nama Pemasok"; }
        td:nth-of-type(10):before { content: "Lokasi"; }
        td:nth-of-type(11):before { content: "Kondisi"; }
        td:nth-of-type(12):before { content: "Catatan"; }
        td:nth-of-type(13):before { content: "User"; }
        td:nth-of-type(14):before { content: "Create AT"; }
        td:nth-of-type(15):before { content: "Update AT"; }
        td:nth-of-type(16):before { content: "Detail"; }

        /* === FIX: hanya NILAI kolom 6–8 yang rata kanan === */
        td:nth-of-type(6) .v,
        td:nth-of-type(7) .v,
        td:nth-of-type(8) .v {
            display: block;
            text-align: right;
        }


        /* Aksi/Detail tetap rapi */
        .td-action { justify-content: flex-start; }
    }
</style>


<div class="container-laporan">
    <div class="header">
        <h4>Daftar Barang Masuk</h4>
        <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">+ Tambah Barang Masuk</a>
    </div>


    {{-- Filter --}}
    <form method="GET" action="{{ route('barang-masuk.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Cari nama barang"
                value="{{ request('search') }}"
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
            <button type="submit">Filter</button>
            <a href="{{ route('barang-masuk.index') }}" class="back-button">Reset</a>
        </div>
    </form>


    {{-- Tabel Data --}}
    <div class="table-responsive mt-3">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Beli</th>
                    <th>Total Harga</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Nama Pemasok</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Catatan</th>
                    <th>User</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangMasuks as $bm)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $bm->kode_barang }}</td>
                        <td>{{ $bm->item->nama_barang ?? '-' }}</td>
                        <td>{{ $bm->jumlah }}</td>
                        <td>{{ number_format($bm->harga_satuan, 0, ',', '.') }}</td>
                        <td>{{ number_format($bm->total_harga, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($bm->tanggal_kadaluarsa)->format('d-m-Y H:i') }}</td>
                        <td>{{ $bm->pemasok->nama_pemasok ?? '-' }}</td>
                        <td>{{ $bm->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>{{ $bm->kondisi->nama_kondisi ?? '-' }}</td>
                        <td>{{ $bm->catatan }}</td>
                        <td>{{ $bm->user->username ?? '-' }}</td>
                        <td>{{ $bm->created_at->format('d-m-Y') }}</td>
                        <td>{{ $bm->updated_at->format('d-m-Y') }}</td>
                        <td>
                            @if($bm->qr_code)
                                <a href="{{ route('barang-masuk.qr-card', $bm->id) }}"
                                   class="back-button"
                                   style="background-color: #60a5fa; color: white;">
                                    Lihat
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center">Data belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <div style="margin-top:20px; text-align:center;">
        {{ $barangMasuks->links() }}
    </div>
</div>
@endsection
