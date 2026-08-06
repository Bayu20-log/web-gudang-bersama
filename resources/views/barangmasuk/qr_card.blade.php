@extends('layouts.app')


@section('content')
<style>
    .qr-card {
        max-width: 900px;
        display: flex;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 12px;
        margin: 30px auto;
        font-family: 'Segoe UI', sans-serif;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        background-color: #fff;
        align-items: flex-start;
    }
    .qr-left {
        flex: 0 0 250px;
        text-align: center;
        margin-right: 30px;
    }
    .qr-left img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        object-fit: contain;
        border: 1px solid #ccc;
        padding: 5px;
        background-color: #f9f9f9;
    }
    .qr-right {
        flex: 1;
    }
    .qr-card h4 {
        text-align: left;
        margin-bottom: 15px;
    }
    .qr-info {
        font-size: 14px;
        width: 100%;
        border-collapse: collapse;
    }
    .qr-info tr {
        display: flex;
        margin-bottom: 6px;
    }
    .qr-info td:first-child {
        width: 90px;
        font-weight: bold;
    }
    .qr-info td:nth-child(2) {
        padding-right: 5px;
    }
    .qr-info td:last-child {
        flex: 1;
    }
    .qr-info img {
        width: 120px;
        height: auto;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #fafafa;
    }
    .back-btn {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }


    @media (max-width: 768px) {
    .qr-card {
        flex-direction: column;
        align-items: center;
        padding: 15px;
    }


    .qr-left {
        margin-right: 0;
        margin-bottom: 20px;
        flex: unset;
        width: 100%;
        max-width: 250px;
    }


    .qr-right {
        width: 100%;
    }


    .qr-info tr {
        flex-direction: column;
        align-items: flex-start;
    }


    .qr-info td:first-child {
        width: 100%;
        margin-bottom: 2px;
    }


    .qr-info td:nth-child(2) {
        display: none; /* Sembunyikan tanda ':' di mobile */
    }


    .qr-info td:last-child {
        width: 100%;
    }


    .back-btn {
        flex-direction: column;
        width: 100%;
    }


    .back-btn a {
        width: 100%;
        text-align: center;
    }
}


</style>


<div class="qr-card">
    <div class="qr-left">
        @if($barangMasuk->item && $barangMasuk->item->foto && Storage::disk('public')->exists($barangMasuk->item->foto))
            <img src="{{ asset('storage/' . $barangMasuk->item->foto) }}" alt="Foto Barang">
        @else
            <em style="display: block;">Tidak tersedia</em>
        @endif
    </div>


    <div class="qr-right">
        <h4>Detail Barang</h4>
        <table class="qr-info">
            <tr><td>Kode</td><td>:</td><td>{{ $barangMasuk->kode_barang }}</td></tr>
            <tr><td>Nama</td><td>:</td><td>{{ $barangMasuk->item->nama_barang ?? '-' }}</td></tr>
            <tr><td>Jumlah</td><td>:</td><td>{{ $barangMasuk->jumlah }}</td></tr>
            <tr><td>Harga</td><td>:</td><td>Rp {{ number_format($barangMasuk->harga_satuan, 0, ',', '.') }}</td></tr>
            <tr><td>Total</td><td>:</td><td>Rp {{ number_format($barangMasuk->total_harga, 0, ',', '.') }}</td></tr>
            <tr><td>Tanggal</td><td>:</td><td>{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y') }}</td></tr>
            <tr><td>Kondisi</td><td>:</td><td>{{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}</td></tr>
            <tr><td>Petugas</td><td>:</td><td>{{ $barangMasuk->user->name ?? '-' }}</td></tr>
            <tr>
                <td>Kode QR</td><td>:</td>
                <td>
                    @if($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code))
                        <img src="{{ asset('storage/' . $barangMasuk->qr_code) }}" alt="QR Code">
                    @else
                        <em>Tidak tersedia</em>
                    @endif
                </td>
            </tr>
        </table>


        {{-- Tombol --}}
        <div class="back-btn">
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('barang-masuk.cetak-qr-kecil', $barangMasuk->id) }}" class="btn btn-outline-primary">Cetak QR</a>
            <a href="{{ route('barang-masuk.cetak.pdf', $barangMasuk->id) }}" class="btn btn-outline-primary">Cetak Label</a>
            <a href="{{ route('barang-masuk.cetak-berita-acara', $barangMasuk->id) }}" class="btn btn-outline-primary" target="_blank">Cetak Berita Acara</a>
        </div>
    </div>
</div>
@endsection
