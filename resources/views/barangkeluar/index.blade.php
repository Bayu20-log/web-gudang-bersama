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
    
    .btn-action { 
        width: 36px; 
        height: 36px; 
        border-radius: 8px; 
        font-size: 14px; 
        cursor: pointer; 
        text-decoration: none; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        border: none; 
        transition: 0.2s; 
    }
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
        
        /* Sembunyikan kolom yang tidak perlu */
        td:nth-of-type(1), 
        td:nth-of-type(3), 
        td:nth-of-type(4), 
        td:nth-of-type(6), 
        td:nth-of-type(7), 
        td:nth-of-type(9), 
        td:nth-of-type(10), 
        td:nth-of-type(11), 
        td:nth-of-type(12) 
        {
            display: none !important;
        }

        /* Tampilkan kolom utama */
        td:nth-of-type(2):before  { content: "Nama Barang"; }
        td:nth-of-type(5):before  { content: "Jumlah Keluar"; }
        td:nth-of-type(8):before  { content: "Catatan"; }
        td:nth-of-type(13):before { content: "Aksi"; } 
        
        .td-action { justify-content: flex-start; flex-wrap: wrap; gap: 8px;}
    }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Barang &rsaquo; Daftar Barang Keluar</h4>
        <!-- Tombol tambah tetap teks agar mencolok -->
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Barang Keluar</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('barang-keluar.index') }}" method="GET" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik kode, nama, atau penerima...">
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
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-outline-dark fw-bold px-4" style="display: inline-flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>

    <div class="mb-3 text-muted fw-medium">
        Total Data: <span class="badge bg-secondary">{{ $barangKeluars->total() }}</span>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Jml Keluar</th>
                    <th>Harga Jual</th>
                    <th>Total Harga</th>
                    <th>Catatan</th>
                    <th>Penerima</th>
                    <th>User</th>
                    <th>Tujuan</th>
                    <th>Tgl Keluar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangKeluars as $keluar)
                    <tr>
                        <td class="fw-medium text-secondary">{{ $keluar->kode_barang }}</td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $keluar->item->nama_barang ?? '-' }}</td>
                        <td>{{ $keluar->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>{{ $keluar->kondisi->nama_kondisi ?? '-' }}</td>
                        <td>{{ $keluar->jumlah_keluar }}</td>
                        <td>Rp{{ number_format($keluar->harga_jual, 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp{{ number_format($keluar->total_harga_jual, 0, ',', '.') }}</td>
                        <td>{{ $keluar->catatan ?? '-' }}</td>
                        <td>{{ $keluar->penerima ?? '-' }}</td>
                        <td>{{ $keluar->user->username ?? '-' }}</td>
                        <td>{{ $keluar->lokasi_tujuan ?? '-' }}</td>
                        <td>{{ $keluar->tanggal_keluar ? \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y H:i') : '-' }}</td>
                        <td class="td-action">
                            <a href="{{ route('barang-keluar.detail', $keluar->id) }}" class="btn-action text-white shadow-sm" style="background-color: #3b82f6;" title="Lihat Detail Transaksi">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="13" class="text-center py-5 text-muted fw-medium"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>Tidak ada data barang keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $barangKeluars->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection