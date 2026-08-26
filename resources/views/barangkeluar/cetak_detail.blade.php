<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Transaksi Barang Keluar</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11pt; color: #000; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 26pt; font-weight: bold; color: #f97316; letter-spacing: 1px; }
        .kop-surat h1 span { color: #000; }
        .judul { text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px; color: #000; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { text-align: left; padding: 10px; vertical-align: middle; border-bottom: 1px solid #000; color: #000; }
        th { width: 35%; font-weight: bold; background-color: #fff; }
        .highlight { font-size: 14pt; font-weight: bold; color: #f97316; }
        .footer-note { text-align: center; margin-top: 40px; font-size: 10pt; color: #000; font-style: italic; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h1>RAK<span>SAKTI</span></h1>
    </div>

    <div class="judul">Ringkasan Transaksi Keluar</div>

    @php
        $hargaRata = \App\Models\BarangMasuk::where('kode_barang', $barangKeluar->kode_barang)
            ->where('id_lokasi', $barangKeluar->id_lokasi)
            ->where('id_kondisi', $barangKeluar->id_kondisi)
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->value('harga_satuan') ?? 0;
    @endphp

    <table>
        <tr><th>ID Transaksi</th><td><strong>TRX-OUT-{{ str_pad($barangKeluar->id, 5, '0', STR_PAD_LEFT) }}</strong></td></tr>
        <tr><th>Tanggal Keluar</th><td>{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y, H:i') }}</td></tr>
        <tr><th>Kode Produk</th><td>{{ $barangKeluar->kode_barang }}</td></tr>
        <tr><th>Nama Produk</th><td><strong>{{ $barangKeluar->item->nama_barang ?? '-' }}</strong></td></tr>
        <tr><th>Kondisi Produk</th><td>{{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}</td></tr>
        <tr><th>Lokasi Asal</th><td>{{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}</td></tr>
        <tr><th>Penerima</th><td>{{ $barangKeluar->penerima ?? '-' }}</td></tr>
        <tr><th>Tujuan Pengiriman</th><td>{{ $barangKeluar->lokasi_tujuan ?? '-' }}</td></tr>
        <tr><th>Petugas Pencatat</th><td>{{ $barangKeluar->user->name ?? $barangKeluar->user->username ?? '-' }}</td></tr>
        <tr><th>Catatan</th><td>{{ $barangKeluar->catatan ?? '-' }}</td></tr>
    </table>

    <table style="border: 3px solid #000; margin-top: 25px;">
        <tr>
            <th style="background-color: #fff; color: #000; width: auto; border-bottom: 1px solid #000;">Jumlah Keluar</th>
            <td style="text-align: right; font-size: 12pt; border-bottom: 1px solid #000;"><strong>{{ $barangKeluar->jumlah_keluar }}</strong> {{ $barangKeluar->item->satuan->nama_satuan ?? 'Unit' }}</td>
        </tr>
        <tr>
            <th style="background-color: #fff; color: #000; width: auto; border-bottom: 1px solid #000;">Harga Jual per Unit</th>
            <td style="text-align: right; border-bottom: 1px solid #000;">Rp {{ number_format($barangKeluar->harga_jual, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th style="background-color: #fff; color: #000; width: auto;">Total Transaksi</th>
            <td style="text-align: right;" class="highlight">Rp {{ number_format($barangKeluar->total_harga_jual, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini dihasilkan secara otomatis oleh sistem RAKSAKTI pada {{ now()->translatedFormat('d F Y H:i') }}.
    </div>
</body>
</html>