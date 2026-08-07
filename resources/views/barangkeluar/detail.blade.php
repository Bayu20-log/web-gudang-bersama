@extends('layouts.app')


@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-center">
        <div class="card shadow rounded w-100" style="max-width: 600px;">
            <div class="card-body">
                <h4 class="mb-4 text-center">Detail Barang Keluar</h4>


                @php
                    // ambil harga_satuan terakhir dari Barang Masuk untuk kombinasi yang sama
                    $hargaRata = \App\Models\BarangMasuk::where('kode_barang', $barangKeluar->kode_barang)
                        ->where('id_lokasi', $barangKeluar->id_lokasi)
                        ->where('id_kondisi', $barangKeluar->id_kondisi)
                        ->orderByDesc('tanggal_masuk')
                        ->orderByDesc('id')
                        ->value('harga_satuan') ?? 0;
                @endphp


                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 40%;">Kode</th>
                        <td>: {{ $barangKeluar->kode_barang }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>: {{ $barangKeluar->item->nama_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>: {{ $barangKeluar->jumlah_keluar }}</td>
                    </tr>
                    <tr>
                        <th>Harga Rata-Rata</th>
                        <td>: Rp {{ number_format($hargaRata, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual / Unit</th>
                        <td>: Rp {{ number_format($barangKeluar->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>: Rp {{ number_format($barangKeluar->total_harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Keluar</th>
                        <td>: {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Kondisi</th>
                        <td>: {{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Awal</th>
                        <td>: {{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Tujuan</th>
                        <td>: {{ $barangKeluar->lokasi_tujuan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>: {{ $barangKeluar->catatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Penerima</th>
                        <td>: {{ $barangKeluar->penerima ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Petugas</th>
                        <td>: {{ $barangKeluar->user->username ?? '-' }}</td>
                    </tr>
                </table>


                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary px-4">← Kembali</a>
                    <div class="d-flex">
                        <a href="{{ route('barang-keluar.cetak-detail', $barangKeluar->id) }}" target="_blank" class="btn btn-outline-primary me-2">Cetak PDF</a>
                        <a href="{{ route('barang-keluar.cetak-ba', $barangKeluar->id) }}" target="_blank" class="btn btn-primary">Cetak BA</a>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection
