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
        <h4 class="fw-bold" style="color: #1e293b;">Barang &rsaquo; Daftar Barang Masuk</h4>
        <a href="{{ route('barang-masuk.create') }}" class="btn btn-orange fw-bold px-4 py-2 shadow-sm">+ Tambah Barang Masuk</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter versi desktop --}}
    <form method="GET" action="{{ route('barang-masuk.index') }}" class="filter-form filter-form-inline">
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

    {{-- Tombol pemicu filter versi HP --}}
    <button type="button" class="filter-trigger-btn" data-bs-toggle="offcanvas" data-bs-target="#filterBarangMasuk">
        <i class="fa-solid fa-sliders"></i> Filter
        @if(request('search') || request('lokasi'))
            <span class="badge" style="background:#f97316;">{{ collect([request('search'), request('lokasi')])->filter()->count() }}</span>
        @endif
    </button>

    {{-- Panel filter bottom-sheet (khusus HP) --}}
    <div class="offcanvas offcanvas-bottom offcanvas-filter" tabindex="-1" id="filterBarangMasuk" style="height:auto; max-height:80vh; border-radius:20px 20px 0 0;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Barang Masuk</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <form method="GET" action="{{ route('barang-masuk.index') }}" class="d-flex flex-column">
            <div class="offcanvas-body">
                <label>Cari Barang</label>
                <input type="text" name="search" class="form-control" placeholder="Ketik nama atau kode barang..." value="{{ request('search') }}">

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
                <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-dark fw-bold flex-fill">Reset</a>
                <button type="submit" class="btn btn-orange fw-bold flex-fill">Terapkan</button>
            </div>
        </form>
    </div>

    {{-- ================= TAMPILAN DESKTOP (tabel) ================= --}}
    <div class="table-wrapper desktop-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Kode Barang</th><th>Nama Barang</th><th>Jumlah</th>
                    <th>Harga Beli</th><th>Total Harga</th><th>Tgl Masuk</th><th>Kadaluarsa</th>
                    <th>Pemasok</th><th>Lokasi</th><th>Kondisi</th><th>Catatan</th><th>User</th><th>Aksi QR</th>
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
                                <a href="{{ route('barang-masuk.qr-card', $bm->id) }}" class="btn-action text-white shadow-sm" style="background-color: #3b82f6;" title="Lihat QR Code">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2h2v2H2V2Z"/><path d="M6 0v6H0V0h6ZM5 1H1v4h4V1ZM4 12H2v2h2v-2Z"/><path d="M6 10v6H0v-6h6Zm-5 1v4h4v-4H1Zm11-9h2v2h-2V2Z"/><path d="M10 0v6h6V0h-6Zm5 1v4h-4V1h4ZM8 1V0h1v2H8v2H7V1h1Zm0 5V4h1v2H8ZM6 8V7h1V6h1v2h1V7h5v1h-4v1H7V8H6Zm0 0v1H2V8H1v1H0V7h3v1h3Zm10 1h-1V7h1v2Zm-1 0h-1v2h2v-1h-1V9Zm-4 0h2v1h-1v1h-1V9Zm2 3v-1h-1v1h-1v1H9v1h3v-2h1Zm0 0h3v1h-2v1h-1v-2Zm-4-1v1h1v-2H7v1h2Z"/><path d="M7 12h1v3h4v1H7v-4Zm9 2v2h-3v-1h2v-1h1Z"/></svg>
                                </a>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">Kosong</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center py-5 text-muted fw-medium"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>Data transaksi masuk belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= TAMPILAN HP (kartu + menu titik tiga) ================= --}}
    <div class="mobile-card-list">
        @forelse($barangMasuks as $bm)
            <div class="mobile-card-item">
                <div>
                    <div class="mc-title">{{ $bm->item->nama_barang ?? '-' }}</div>
                    <div class="mc-sub">{{ $bm->lokasi->nama_lokasi ?? '-' }} &middot; {{ $bm->pemasok->nama_pemasok ?? '-' }}</div>
                    <div class="mc-sub">{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d M Y H:i') }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold mb-1" style="color:#16a34a;">+{{ $bm->jumlah }}</div>
                    <div class="dropdown">
                        <button class="btn-action-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
                            <li>
                                @if($bm->qr_code)
                                    <a class="dropdown-item" href="{{ route('barang-masuk.qr-card', $bm->id) }}">
                                        <i class="fa-solid fa-qrcode" style="color:#3b82f6;"></i> Lihat QR
                                    </a>
                                @else
                                    <span class="dropdown-item disabled">QR tidak tersedia</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted fw-medium">
                <i class="fa-solid fa-box-open mb-2" style="font-size:28px; color:#cbd5e1;"></i><br>
                Data transaksi masuk belum tersedia.
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $barangMasuks->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
