@extends('layouts.app')




@section('content')
<style>
    .container-laporan {
        max-width: 1200px;
        margin: 42px auto 24px;
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




    .back-button {
        padding: 8px 14px;
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        text-align: center;
    }
    .back-button:hover { background-color: #d1d5db; }




    /* Filter Form */
    .filter-form {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }
    .filter-form .form-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 200px;
    }
    .filter-form label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .filter-form input,
    .filter-form select {
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }




    .filter-form .action-group {
        display: flex;
        flex-direction: row;
        gap: 10px;
        flex-shrink: 0;
    }




    .filter-form button {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .filter-form button:hover { background-color: #2563eb; }




    /* Export Buttons */
    .export-buttons { margin-bottom: 20px; }
    .export-buttons a {
        padding: 8px 12px;
        background-color: #2563eb;
        color: #fff;
        border-radius: 6px;
        margin-right: 10px;
        text-decoration: none;
        font-size: 14px;
    }
    .export-buttons a:hover { background-color: #1d4ed8; }




    /* Table */
    table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    th, td { padding: 12px; background-color: white; text-align: center; }
    th { background-color: #f3f4f6; font-weight: 600; border-bottom: 1px solid #ddd; }
    th a { color: inherit; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); }
</style>




<div class="container-laporan">
    <div class="header">
        <h2>Laporan Arus Barang</h2>
        <a href="{{ route('laporan') }}" class="back-button">← Kembali ke Laporan Stok</a>
    </div>




    {{-- Filter Form --}}
    <form class="filter-form" method="GET" action="{{ route('laporan.arus') }}">
        <div class="form-group">
            <label>Lokasi:</label>
            <select name="lokasi" class="form-control">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                        {{ $lokasi->nama_lokasi }}
                    </option>
                @endforeach
            </select>
        </div>




        <div class="form-group">
            <label>Pencarian (Nama/Kode/Pihak):</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang atau pihak...">
        </div>




        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('laporan.arus') }}" class="back-button">Reset Filter</a>
        </div>
    </form>




    <div class="export-buttons">
        <a href="{{ route('laporan.arus.pdf', request()->query()) }}" target="_blank">📄 Cetak PDF</a>
        <a href="{{ route('laporan.arus.excel', request()->query()) }}">📊 Export Excel</a>
    </div>




    <div class="table-responsive">
    <table>
        <thead>
            @php
                $sortBy = request('sort_by');
                $columns = [
                    'kode_barang'   => 'Kode Barang',
                    'nama_barang'   => 'Nama Barang',
                    'harga_dasar'   => 'Harga Dasar',
                    'tanggal'       => 'Tanggal Transaksi',
                    'jumlah_masuk'  => 'Jumlah Masuk',
                    'jumlah_keluar' => 'Jumlah Keluar',
                    'total_barang'  => 'Total Barang',
                    'lokasi'        => 'Lokasi',
                    'pihak'         => 'Pihak'
                ];
            @endphp
            <tr>
                @foreach($columns as $key => $label)
                    <th>
                        <a href="{{ route('laporan.arus', array_merge(request()->all(), [
                            'sort_by' => $key,
                            'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'
                        ])) }}">
                            {{ $label }}
                            @if($sortBy === $key)
                                {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                            @else
                                ▲▼
                            @endif
                        </a>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($paginated as $row)
                @php
                    $kode  = $row['kode_barang'];
                    $pihak = $row['pihak'];
                    if (is_string($pihak)) {
                        $json  = json_decode($pihak, true);
                        $pihak = is_array($json) ? $json : ['nama' => $pihak];
                    }
                @endphp
                <tr>
                    <td>{{ $kode }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td>Rp {{ number_format($row['harga_dasar']) }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('j/n/Y H:i:s') }}</td>
                    <td>{{ $row['jumlah_masuk'] }}</td>
                    <td>{{ $row['jumlah_keluar'] }}</td>
                    <td>{{ $row['total_barang'] }}</td>
                    <td>{{ is_array($row['lokasi']) ? $row['lokasi']['nama_lokasi'] ?? '-' : $row['lokasi'] }}</td>
                    <td>{{ $pihak['nama_pemasok'] ?? $pihak['nama_penerima'] ?? $pihak['nama'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>




    {{-- Pagination + summary (tanpa duplikasi) --}}
    @php
        $isPaginator = $paginated instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $total = $isPaginator ? $paginated->total()
                              : (is_countable($paginated) ? count($paginated) : collect($paginated)->count());
        $first = $isPaginator ? ($paginated->firstItem() ?? ($total ? 1 : 0)) : ($total ? 1 : 0);
        $last  = $isPaginator ? ($paginated->lastItem()  ?? $total) : $total;
    @endphp
    <div style="margin-top:20px; text-align:center;">
        <div class="small text-muted">
            Showing {{ $first }} to {{ $last }} of {{ $total }} results
        </div>
        <div class="ms-auto">
            @if($isPaginator)
                {!! $paginated->appends(request()->query())->links('pagination::bootstrap-5') !!}
            @endif
        </div>
    </div>
</div>
@endsection
