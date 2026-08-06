<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Detail Barang Keluar</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 6px 10px; vertical-align: top; }
        .label { width: 30%; }
    </style>
</head>
<body>


    <div class="header">Detail Barang Keluar</div>


    <table>
        <tr>
            <th class="label">Kode</th>
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
        <!-- ✅ Tambahan Harga Dasar -->
        <tr>
            <th>Harga Dasar</th>
            <td>: Rp {{ number_format($barangKeluar->item->harga_dasar ?? 0, 0, ',', '.') }}</td>
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
            <td>: {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y') }}</td>
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
            <th>Penerima</th>
            <td>: {{ $barangKeluar->penerima ?? '-' }}</td>
        </tr>
        <tr>
            <th>Petugas</th>
            <td>: {{ $barangKeluar->user->username ?? '-' }}</td>
        </tr>
        <tr>
            <th>Catatan</th>
            <td>: {{ $barangKeluar->catatan ?? '-' }}</td>
        </tr>
    </table>


</body>
</html>
