@extends('layouts.app')

@section('content')
<style>
  .detail-pasokan-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 16px;
  }

  .detail-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }

  .detail-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 16px;
  }

  .detail-title-group h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px 0;
  }

  .detail-title-group p {
    font-size: 13px;
    color: #64748b;
    margin: 0;
  }

  .filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
  }

  .filter-form select,
  .filter-form input[type="date"] {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 13px;
    color: #334155;
    outline: none;
    background-color: #fff;
  }

  .btn-filter-submit {
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .btn-filter-submit:hover {
    background: #1d4ed8;
  }

  .btn-pdf-export {
    background: #ef4444;
    color: #ffffff;
    border-radius: 6px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s;
  }

  .btn-pdf-export:hover {
    background: #dc2626;
    color: #ffffff;
  }

  .btn-back-dash {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
  }

  .btn-back-dash:hover {
    background: #e2e8f0;
    color: #1e293b;
  }

  .table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
  }

  .pasokan-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13px;
  }

  .pasokan-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
  }

  .pasokan-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
  }

  .pasokan-table tr:last-child td {
    border-bottom: none;
  }

  .badge-freq {
    background: #eff6ff;
    color: #2563eb;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    display: inline-block;
  }
</style>

<div class="detail-pasokan-container">
    <!-- Header & Filter Bar -->
    <div class="detail-header-card">
        <div class="detail-header-top">
            <div class="detail-title-group">
                <h2>Laporan Detail Pasokan Pemasok</h2>
                <p>Monitoring kontribusi barang masuk per pemasok periode <b>{{ $formattedDateFrom }}</b> s/d <b>{{ $formattedDateTo }}</b></p>
            </div>
            <a href="{{ route('dashboard.gudang', ['date_from' => $formattedDateFrom, 'date_to' => $formattedDateTo]) }}" class="btn-back-dash">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <div class="filter-bar">
            <form action="{{ route('pemasok.detail-pasokan') }}" method="GET" class="filter-form">
                <select name="pemasok_id">
                    <option value="">-- Semua Pemasok --</option>
                    @foreach($listPemasok as $p)
                        <option value="{{ $p->id }}" {{ ($selectedPemasokId ?? '') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_pemasok }}
                        </option>
                    @endforeach
                </select>

                <label style="font-size: 13px; color: #64748b; font-weight: 500;">Tanggal:</label>
                <input type="date" name="date_from" value="{{ $formattedDateFrom }}">
                <span style="font-size: 13px; color: #94a3b8;">s/d</span>
                <input type="date" name="date_to" value="{{ $formattedDateTo }}">

                <button type="submit" class="btn-filter-submit">Terapkan Filter</button>         
            </form>

            <a href="{{ route('pemasok.export-pdf', ['pemasok_id' => $selectedPemasokId ?? '', 'date_from' => $formattedDateFrom, 'date_to' => $formattedDateTo]) }}" 
               target="_blank" class="btn-pdf-export">
                &darr; Export PDF
            </a>
        </div>
    </div>

    <!-- Tabel Data Pemasok -->
    <div class="table-card">
        <div style="overflow-x: auto;">
            <table class="pasokan-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Nama Pemasok</th>
                        <th>Kontak / PIC</th>
                        <th style="text-align: center;">Frekuensi Transaksi</th>
                        <th style="text-align: right;">Total Barang Masuk</th>
                        <th style="text-align: right;">Total Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemasoks as $index => $pemasok)
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: #94a3b8;">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">{{ $pemasok->nama_pemasok }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $pemasok->email ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $pemasok->nama_pic ?? '-' }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $pemasok->no_telepon ?? '-' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-freq">
                                    {{ number_format($pemasok->total_transaksi) }} Kali
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">
                                {{ number_format($pemasok->total_barang_masuk, 0, ',', '.') }} Unit
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #16a34a;">
                                Rp {{ number_format($pemasok->total_nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 32px; text-align: center; color: #94a3b8;">
                                Tidak ada data pasokan barang masuk pada periode tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection