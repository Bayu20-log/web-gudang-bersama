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

    /* PERBAIKAN TEMA FILTER */
    form.filter-form { background-color: #ffffff; border: none; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 250px; flex-grow: 1; }
    .filter-form label { font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;}
    .filter-form input, .filter-form select { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: 0.3s; }
    .filter-form input:focus, .filter-form select:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
    
    /* PERBAIKAN TEMA TABEL */
    .table-wrapper { width: 100%; overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }
    tr:hover { background-color: #f8fafc; }

    /* PERBAIKAN TOMBOL AKSI JADI LEBIH JELAS */
    .btn-action { padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap; border: none; transition: 0.2s; }
    .btn-action:hover { opacity: 0.85; transform: translateY(-2px); }

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
        
        td:nth-of-type(1):before  { content: "No"; }
        td:nth-of-type(2):before  { content: "Kode Barang"; }
        td:nth-of-type(3):before  { content: "Nama Barang"; }
        td:nth-of-type(4):before  { content: "Jumlah"; }
        td:nth-of-type(5):before  { content: "Harga Beli"; }
        td:nth-of-type(6):before  { content: "Total Harga"; }
        td:nth-of-type(7):before  { content: "Tanggal Masuk"; }
        td:nth-of-type(8):before  { content: "Kadaluarsa"; }
        td:nth-of-type(9):before  { content: "Pemasok"; }
        td:nth-of-type(10):before { content: "Lokasi"; }
        td:nth-of-type(11):before { content: "Kondisi"; }
        td:nth-of-type(12):before { content: "Catatan"; }
        td:nth-of-type(13):before { content: "User"; }
        td:nth-of-type(14):before { content: "Aksi QR"; }
        .td-action { justify-content: flex-start; flex-wrap: wrap; gap: 8px;}
    }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Barang &rsaquo; Daftar Barang Masuk</h4>
        <a href="{{ route('barang-masuk.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Barang Masuk</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Tema Baru --}}
    <form method="GET" action="{{ route('barang-masuk.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input type="text" id="search" name="search" placeholder="Ketik nama atau kode barang..." value="{{ request('search') }}">
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
        <div class="btn-action-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-orange fw-bold px-4">Filter</button>
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-dark fw-bold px-4" style="display: inline-flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>

    {{-- Tabel Tema Baru --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Beli</th>
                    <th>Total Harga</th>
                    <th>Tgl Masuk</th>
                    <th>Kadaluarsa</th>
                    <th>Pemasok</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Catatan</th>
                    <th>User</th>
                    <th>Aksi QR</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangMasuks as $bm)
                    <tr>
                        <td>{{ ($barangMasuks->currentPage() - 1) * $barangMasuks->perPage() + $loop->iteration }}</td>
                        <td class="fw-medium text-secondary">{{ $bm->kode_barang }}</td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $bm->item->nama_barang ?? '-' }}</td>
                        <td>{{ $bm->jumlah }}</td>
                        <td>{{ number_format($bm->harga_satuan, 0, ',', '.') }}</td>
                        <td class="fw-bold">{{ number_format($bm->total_harga, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($bm->tanggal_kadaluarsa)->format('d-m-Y H:i') }}</td>
                        <td>{{ $bm->pemasok->nama_pemasok ?? '-' }}</td>
                        <td>{{ $bm->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>{{ $bm->kondisi->nama_kondisi ?? '-' }}</td>
                        <td>{{ $bm->catatan }}</td>
                        <td>{{ $bm->user->username ?? '-' }}</td>
                        <td class="td-action">
                            @if($bm->qr_code)
                                <a href="{{ route('barang-masuk.qr-card', $bm->id) }}" class="btn-action text-white shadow-sm" style="background-color: #3b82f6;">
                                    <i class="fa-solid fa-qrcode"></i> Lihat QR
                                </a>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">Kosong</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center py-5 text-muted fw-medium"><i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-light"></i>Data barang belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        {{ $barangMasuks->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection