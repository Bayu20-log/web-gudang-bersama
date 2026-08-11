@extends('layouts.app')
 
@section('content')
<style>
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
 
    .btn-tambah {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: #fff;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }
    .btn-tambah:hover { background-color: #2563eb; }
 
    /* Filter Form */
    .filter-form {
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
        flex: 1;
        min-width: 180px;
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
 
    /* Table */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    th, td {
        padding: 12px;
        background-color: white;
        text-align: center;
    }
    th {
        background-color: #f3f4f6;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
    }
    th a { color: inherit; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); border-radius: 8px; }
 
    .badge-model {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }
    .badge-ses { background-color: #10b981; }
    .badge-hw { background-color: #8b5cf6; }
    .badge-arima { background-color: #f59e0b; }
 
    .action-icons a {
        margin: 0 4px;
        text-decoration: none;
        font-size: 13px;
    }
 
    @media(max-width: 768px) {
        .filter-form { flex-direction: column; gap: 12px; align-items: stretch; }
        .filter-form .form-group,
        .filter-form .action-group { width: 100%; }
        .filter-form .action-group { flex-direction: column; }
        .filter-form .action-group .back-button,
        .filter-form .action-group button { width: 100%; }
        .container-laporan { padding: 15px 10px; }
    }
</style>
 
<div class="container-laporan">
    <div class="header">
        <h2>Riwayat Prediksi Barang Keluar</h2>
        <a href="{{ route('prediksi.create') }}" class="btn-tambah">+ Buat Prediksi Baru</a>
    </div>
 
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
 
    {{-- Filter Form --}}
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" value="{{ request('nama_barang') }}">
        </div>
        <div class="form-group">
            <label>Model:</label>
            <select name="model_used">
                <option value="">-- Semua Model --</option>
                <option value="SES" {{ request('model_used') == 'SES' ? 'selected' : '' }}>SES</option>
                <option value="Holt-Winters" {{ request('model_used') == 'Holt-Winters' ? 'selected' : '' }}>Holt-Winters</option>
                <option value="ARIMA" {{ request('model_used') == 'ARIMA' ? 'selected' : '' }}>ARIMA</option>
            </select>
        </div>
        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('prediksi.index') }}" class="back-button">Reset Filter</a>
        </div>
    </form>
 
    @php
        $sortBy = $sortBy ?? request('sort_by');
        $columns = [
            'created_at' => 'Tanggal Prediksi',
            'model_used' => 'Model Digunakan',
            'rmse' => 'RMSE',
            'mape' => 'MAPE',
            'mae' => 'MAE',
        ];
    @endphp
 
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('prediksi.index', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
                                {{ $label }}
                                @if($sortBy === $key)
                                    {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                                @else
                                    ▲▼
                                @endif
                            </a>
                        </th>
                    @endforeach
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $run)
                    <tr>
                        <td>{{ $run->item->nama_barang ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($run->created_at)->format('d M Y H:i') }}</td>
                        <td>
                            @php
                                $badgeClass = match($run->model_used) {
                                    'SES' => 'badge-ses',
                                    'Holt-Winters' => 'badge-hw',
                                    'ARIMA' => 'badge-arima',
                                    default => 'badge-ses',
                                };
                            @endphp
                            <span class="badge-model {{ $badgeClass }}">{{ $run->model_used }}</span>
                        </td>
                        <td>{{ number_format($run->rmse, 3) }}</td>
                        <td>{{ number_format($run->mape, 2) }}%</td>
                        <td>{{ number_format($run->mae, 3) }}</td>
                        <td class="action-icons">
                            <a href="{{ route('prediksi.show', $run->id) }}">🔍 Detail</a>
                            <a href="{{ route('prediksi.pdf', $run->id) }}" target="_blank">📄 PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada riwayat prediksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
 
    {{-- Pagination + summary --}}
    @php
        $total = $data->total();
        $first = $data->firstItem() ?? 0;
        $last  = $data->lastItem() ?? 0;
    @endphp
    <div style="margin-top:20px; text-align:center;">
        <div class="small text-muted">
            Showing {{ $first }} to {{ $last }} of {{ $total }} results
        </div>
        <div class="ms-auto">
            {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
@endsection