@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 1px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; font-weight: 600;}
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }
    
    .container-laporan { max-width: 1200px; margin: auto; padding: 30px 20px; font-family: 'Segoe UI', sans-serif; }
    .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }

    /* TEMA FILTER */
    form.filter-form { background-color: #ffffff; border: none; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 200px; flex-grow: 1; }
    .filter-form label { font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;}
    .filter-form input, .filter-form select { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: 0.3s; }
    .filter-form input:focus, .filter-form select:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
    
    /* TEMA TABEL */
    .table-wrapper { width: 100%; overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    table { width: 100%; border-collapse: collapse; min-width: 900px; }
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }
    th a { color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
    th a:hover { color: #f97316; }
    tr:hover { background-color: #f8fafc; }
    .bg-stok-akhir { 
        background-color: #22c55e !important;
        color: #ffffff !important; 
    }

    @media(max-width: 768px) {
        .header { flex-direction: column; align-items: flex-start; }
        .filter-form { flex-direction: column; }
        .filter-form .form-group, .btn-action-group { width: 100%; }
        .btn-action-group { display: flex; gap: 10px; }
        .btn-action-group button, .btn-action-group a { flex: 1; text-align: center; justify-content: center; }
        
        table { min-width: 100%; }
        table, thead, tbody, th, td, tr { display: block; width: 100%; }
        thead { display: none; }
        tr { margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; background-color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        td { border: none !important; text-align: left; padding: 8px 0 8px 45%; position: relative; }
        td:before { position: absolute; top: 8px; left: 0; width: 40%; white-space: nowrap; font-weight: 600; color: #4b5563; }
        
        td:nth-of-type(1):before { content: "Kode Barang"; }
        td:nth-of-type(2):before { content: "Nama Barang"; }
        td:nth-of-type(3):before { content: "Harga Dasar"; }
        td:nth-of-type(4):before { content: "Total Masuk"; }
        td:nth-of-type(5):before { content: "Total Keluar"; }
        td:nth-of-type(6):before { content: "Stok Akhir"; }
        td:nth-of-type(7):before { content: "Lokasi"; }
        td:nth-of-type(8):before { content: "Username"; }
    }
</style>

@php $role = auth()->user()->role; @endphp

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold mb-0" style="color: #1e293b;">Laporan &rsaquo; Stok &amp; Arus Barang</h4>
        @if ($role === 'gudang')
            <div class="report-toggle">
                <a href="{{ route('laporan') }}" class="report-toggle-btn active">Stok Barang</a>
                <a href="{{ route('laporan.arus') }}" class="report-toggle-btn">Arus Barang</a>
            </div>
        @endif
    </div>

    {{-- Search bar + tombol filter --}}
    <div class="search-filter-bar">
        <form method="GET" class="search-bar-form">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
            <input type="text" name="nama_barang" placeholder="Cari nama barang..." value="{{ request('nama_barang') }}" onchange="this.form.submit()">
            @if(request('kode_barang'))<input type="hidden" name="kode_barang" value="{{ request('kode_barang') }}">@endif
            @if(request('lokasi'))<input type="hidden" name="lokasi" value="{{ request('lokasi') }}">@endif
        </form>
        <button type="button" class="filter-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#filterLaporanStok" title="Filter">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05Zm-4.9 5a2.5 2.5 0 0 1 4.9 0H16v1H9.05a2.5 2.5 0 0 1-4.9 0H0V8h4.15Zm.9.5a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0ZM.5 12a2.5 2.5 0 0 1 4.9 0H16v1H5.4a2.5 2.5 0 0 1-4.9 0H0v-1h.5Z"/></svg>
            @if(request('kode_barang') || request('lokasi'))<span class="filter-dot"></span>@endif
        </button>
    </div>

    {{-- Panel filter bottom-sheet (khusus HP) --}}
    <div class="offcanvas offcanvas-bottom offcanvas-filter" tabindex="-1" id="filterLaporanStok" style="height:auto; max-height:80vh; border-radius:20px 20px 0 0;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Laporan Stok</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <form method="GET" class="d-flex flex-column">
            <div class="offcanvas-body">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" placeholder="Ketik nama barang..." value="{{ request('nama_barang') }}">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" class="form-control" placeholder="Ketik kode..." value="{{ request('kode_barang') }}">
                <label>Lokasi</label>
                <select name="lokasi" class="form-select">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach($lokasis as $lokasi)
                        @php
                            $lokasiId   = is_array($lokasi) ? ($lokasi['id'] ?? $lokasi['nama_lokasi']) : $lokasi;
                            $lokasiName = is_array($lokasi) ? ($lokasi['nama_lokasi'] ?? '-') : $lokasi;
                        @endphp
                        <option value="{{ $lokasiId }}" {{ request('lokasi') == $lokasiId ? 'selected' : '' }}>
                            {{ $lokasiName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="offcanvas-footer">
                <a href="{{ route('laporan') }}" class="btn btn-outline-dark fw-bold flex-fill">Reset</a>
                <button type="submit" class="btn btn-orange fw-bold flex-fill">Terapkan</button>
            </div>
        </form>
    </div>

    {{-- Tombol Ekspor ringkas: 1 tombol, terbuka jadi 2 pilihan --}}
    @if ($role === 'gudang')
        <div class="dropdown export-split mb-3">
            <button class="btn btn-outline-dark fw-bold shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5Z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3Z"/></svg> Ekspor
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('laporan.pdf', request()->query()) }}" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ef4444" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('laporan.excel', request()->query()) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#16a34a" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai Excel
                    </a>
                </li>
            </ul>
        </div>
    @endif

    @php
        $sortBy = request('sort_by');
        $columns = [
            'kode_barang' => 'Kode Barang',
            'nama_barang' => 'Nama Barang',
            'harga_dasar' => 'Harga Dasar',
            'total_masuk' => 'Total Masuk',
            'total_keluar' => 'Total Keluar',
            'stok_akhir' => 'Stok Akhir',
            'lokasi' => 'Lokasi',
            'username' => 'Username',
        ];
    @endphp

    {{-- Tabel --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('laporan', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
                                {{ $label }}
                                @if($sortBy === $key)
                                    <i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>
                                @endif
                            </a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td class="fw-medium text-secondary">{{ $item['kode_barang'] }}</td>
                        <td class="fw-bold text-dark">{{ $item['nama_barang'] }}</td>
                        <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                        <td class="text-success fw-bold">+{{ $item['total_masuk'] }}</td>
                        <td class="text-danger fw-bold">-{{ $item['total_keluar'] }}</td>
                        <td><span class="badge bg-stok-akhir px-3 py-2 rounded-pill fs-6">{{ $item['stok_akhir'] }}</span></td>
                        <td>{{ is_array($item['lokasi']) ? ($item['lokasi']['nama_lokasi'] ?? '-') : $item['lokasi'] }}</td>
                        <td>{{ $item['username'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted fw-medium"><i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-light"></i>Data laporan stok belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $isPaginator = $data instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $total = $isPaginator ? $data->total() : (is_countable($data) ? count($data) : collect($data)->count());
        $first = $isPaginator ? ($data->firstItem() ?? ($total ? 1 : 0)) : ($total ? 1 : 0);
        $last  = $isPaginator ? ($data->lastItem()  ?? $total) : $total;
    @endphp

    <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="small text-muted mb-2 mb-md-0 fw-medium">
            Menampilkan {{ $first }} - {{ $last }} dari {{ $total }} data
        </div>
        <div>
            @if($isPaginator)
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            @endif
        </div>
    </div>

    @php $chartItems = $isPaginator ? $data->items() : $data; @endphp
    @if ($total > 0)
        <div class="card border-0 shadow-sm mt-5 p-4 rounded-4 bg-white">
            <h5 class="fw-bold mb-4 text-center" style="color:#1e293b;">Grafik Stok Akhir</h5>
            <canvas id="stokChart" height="100"></canvas>
        </div>
        
        <!-- PERBAIKAN: SCRIPT DIPINDAH KE SINI AGAR LANGSUNG DIEKSEKUSI -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const labels   = {!! json_encode(collect($chartItems)->pluck('nama_barang')->values()) !!};
                const stokData = {!! json_encode(collect($chartItems)->pluck('stok_akhir')->values()) !!};
                const ctx = document.getElementById('stokChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Stok Akhir',
                            data: stokData,
                            backgroundColor: '#f97316',
                            borderRadius: 4
                        }]
                    },
                    options: { 
                        responsive: true, 
                        scales: { y: { beginAtZero: true } },
                        plugins: {
                            legend: { display: false } // Menyembunyikan legend ganda agar lebih bersih
                        }
                    }
                });
            });
        </script>
    @endif
</div>
@endsection