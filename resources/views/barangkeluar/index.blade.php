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
        width: 36px; height: 36px; border-radius: 8px; font-size: 14px; cursor: pointer; 
        text-decoration: none; display: inline-flex; align-items: center; justify-content: center; 
        border: none; transition: 0.2s; 
    }
    .btn-action:hover { opacity: 0.85; transform: translateY(-2px); }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Barang &rsaquo; Daftar Barang Keluar</h4>
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Barang Keluar</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Search bar + tombol filter --}}
    <div class="search-filter-bar">
        <form method="GET" action="{{ route('barang-keluar.index') }}" class="search-bar-form">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
            <input type="text" name="search" placeholder="Cari kode, nama, atau penerima..." value="{{ request('search') }}" onchange="this.form.submit()">
            @if(request('lokasi'))<input type="hidden" name="lokasi" value="{{ request('lokasi') }}">@endif
        </form>
        <button type="button" class="filter-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#filterBarangKeluar" title="Filter">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05Zm-4.9 5a2.5 2.5 0 0 1 4.9 0H16v1H9.05a2.5 2.5 0 0 1-4.9 0H0V8h4.15Zm.9.5a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0ZM.5 12a2.5 2.5 0 0 1 4.9 0H16v1H5.4a2.5 2.5 0 0 1-4.9 0H0v-1h.5Z"/></svg>
            @if(request('lokasi'))<span class="filter-dot"></span>@endif
        </button>
    </div>

    {{-- Panel filter bottom-sheet (khusus HP) --}}
    <div class="offcanvas offcanvas-bottom offcanvas-filter" tabindex="-1" id="filterBarangKeluar" style="height:auto; max-height:80vh; border-radius:20px 20px 0 0;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Barang Keluar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <form method="GET" action="{{ route('barang-keluar.index') }}" class="d-flex flex-column">
            <div class="offcanvas-body">
                <label>Cari Barang</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ketik kode, nama, atau penerima...">
                <label>Lokasi</label>
                <div class="filter-chip-group">
                    <label class="filter-chip {{ request('lokasi') ? '' : 'active' }}">
                        <input type="radio" name="lokasi" value="" class="d-none" {{ request('lokasi') ? '' : 'checked' }}> Semua
                    </label>
                    @foreach($lokasis as $lokasi)
                        <label class="filter-chip {{ request('lokasi') == $lokasi->id ? 'active' : '' }}">
                            <input type="radio" name="lokasi" value="{{ $lokasi->id }}" class="d-none" {{ request('lokasi') == $lokasi->id ? 'checked' : '' }}>
                            {{ $lokasi->nama_lokasi }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="offcanvas-footer">
                <a href="{{ route('barang-keluar.index') }}" class="btn btn-outline-dark fw-bold flex-fill">Reset</a>
                <button type="submit" class="btn btn-orange fw-bold flex-fill">Terapkan</button>
            </div>
        </form>
    </div>

    <div class="mb-3 text-muted fw-medium">
        Total Data: <span class="badge bg-secondary">{{ $barangKeluars->total() }}</span>
    </div>

    {{-- ================= TAMPILAN DESKTOP (tabel) ================= --}}
    <div class="table-wrapper desktop-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Kode Barang</th><th>Nama Barang</th><th>Lokasi</th><th>Kondisi</th>
                    <th>Jml Keluar</th><th>Harga Jual</th><th>Total Harga</th><th>Catatan</th>
                    <th>Penerima</th><th>User</th><th>Tujuan</th><th>Tgl Keluar</th><th>Aksi</th>
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

    {{-- ================= TAMPILAN HP (kartu + menu titik tiga) ================= --}}
    <div class="mobile-card-list">
        @forelse($barangKeluars as $keluar)
            <div class="mobile-card-item">
                <div>
                    <div class="mc-title">{{ $keluar->item->nama_barang ?? '-' }}</div>
                    <div class="mc-sub">{{ $keluar->lokasi->nama_lokasi ?? '-' }} &middot; Ke: {{ $keluar->lokasi_tujuan ?? $keluar->penerima ?? '-' }}</div>
                    <div class="mc-sub">{{ $keluar->tanggal_keluar ? \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d M Y H:i') : '-' }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold mb-1" style="color:#ef4444;">-{{ $keluar->jumlah_keluar }}</div>
                    <div class="dropdown">
                        <button class="btn-action-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/></svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('barang-keluar.detail', $keluar->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#3b82f6" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg> Lihat Detail
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted fw-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>
                Tidak ada data barang keluar.
            </div>
        @endforelse
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $barangKeluars->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
