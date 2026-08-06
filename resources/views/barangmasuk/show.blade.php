@extends('layouts.app')




@section('content')
    <h2>Detail Barang Masuk</h2>




    <table class="table table-bordered">
        <tr>
            <th>Tanggal Masuk</th>
            <td>{{ $barangMasuk->tanggal_masuk }}</td>
        </tr>
        <tr>
            <th>Nama Barang</th>
            <td>{{ $barangMasuk->item->nama_barang }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barangMasuk->item->kategori }}</td>
        </tr>
        <tr>
            <th>Pemasok</th>
            <td>{{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}</td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td>{{ $barangMasuk->item->deskripsi ?? '-' }}</td>
        </tr>
        <tr>
            <th>Foto Barang</th>
            <td>
                @if($barangMasuk->item->foto)
                    <img src="{{ asset('storage/' . $barangMasuk->item->foto) }}" alt="Foto Barang" width="150">
                @else
                    <em>Tidak ada foto</em>
                @endif
            </td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $barangMasuk->jumlah }}</td>
        </tr>
        <tr>
            <th>Satuan</th>
            <td>{{ $barangMasuk->item->satuan }}</td>
        </tr>
        <tr>
            <th>Harga Satuan</th>
            <td>Rp {{ number_format($barangMasuk->item->harga_per_unit, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Harga</th>
            <td>Rp {{ number_format($barangMasuk->jumlah * $barangMasuk->item->harga_per_unit, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>{{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        <tr>
            <th>Catatan</th>
            <td>{{ $barangMasuk->catatan ?? '-' }}</td>
        </tr>
        <tr>
            <th>QR Code</th>
            <td>
                @if($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code))
                    <img src="{{ asset('storage/' . $barangMasuk->qr_code) }}" alt="QR Code" width="150">
                @else
                    <em>QR Code belum tersedia</em>
                @endif
            </td>
        </tr>
    </table>




    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Kembali</a>
@endsection
