<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pengeluaran Barang</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11pt; color: #000; line-height: 1.6; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 25px; }
        .kop-surat h1 { margin: 0; font-size: 28pt; font-weight: bold; letter-spacing: 1px; color: #f97316; }
        .kop-surat h1 span { color: #000; }
        .kop-surat p { margin: 5px 0 0 0; font-size: 10pt; color: #000; }
        .judul { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; color: #000; }
        .nomor { text-align: center; font-size: 11pt; margin-bottom: 30px; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        .table-info td { padding: 4px 8px; border: none; vertical-align: top; }
        .table-data { margin-top: 15px; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 10px 8px; text-align: center; }
        .table-data th { background-color: #fff; color: #000; font-weight: bold; text-transform: uppercase; font-size: 10pt; }
        .ttd-container { width: 100%; margin-top: 50px; text-align: center; }
        .ttd-box { width: 48%; display: inline-block; }
        .ttd-name { margin-top: 80px; font-weight: bold; text-decoration: underline; color: #000;}
    </style>
</head>
<body>
    <div class="kop-surat">
        <h1>RAK<span>SAKTI</span></h1>
        <p>Sistem Informasi Manajemen Gudang & Logistik Terpadu</p>
        <p>Email: admin@gudangku.id | Telp: +62 812 3456 7890</p>
    </div>

    <div class="judul">BERITA ACARA PENGELUARAN BARANG</div>
    <div class="nomor">Nomor : BA-KB/{{ str_pad($barangKeluar->id, 4, '0', STR_PAD_LEFT) }}/{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('m/Y') }}</div>

    <p style="text-align: justify;">
        Pada hari ini <strong>{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('l') }}</strong>, 
        tanggal <strong>{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y') }}</strong>, 
        telah dilakukan serah terima pengeluaran barang dari gudang utama kepada:
    </p>

    <table class="table-info" style="width: 80%; margin-left: 20px;">
        <tr><td style="width: 25%;"><strong>Nama Penerima</strong></td><td style="width: 3%;">:</td><td>{{ $barangKeluar->penerima ?? '-' }}</td></tr>
        <tr><td><strong>Tujuan / Divisi</strong></td><td>:</td><td>{{ $barangKeluar->lokasi_tujuan ?? '-' }}</td></tr>
        <tr><td><strong>Lokasi Asal</strong></td><td>:</td><td>{{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}</td></tr>
    </table>

    <p>Adapun rincian barang yang diserahkan adalah sebagai berikut:</p>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Kode Produk</th>
                <th style="width: 45%;">Nama Produk</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 15%;">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $barangKeluar->kode_barang }}</td>
                <td style="text-align: left;">{{ $barangKeluar->item->nama_barang ?? '-' }}</td>
                <td><strong>{{ $barangKeluar->jumlah_keluar }}</strong> {{ $barangKeluar->item->satuan->nama_satuan ?? '' }}</td>
                <td>{{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <p><strong>Catatan Pengeluaran:</strong><br>
    <span style="font-style: italic;">"{{ $barangKeluar->catatan ?: 'Tidak ada catatan khusus.' }}"</span></p>

    <p style="text-align: justify; margin-top: 30px;">
        Demikian berita acara ini dibuat untuk dijadikan bukti pengeluaran barang yang sah.
    </p>

    <div class="ttd-container">
        <div class="ttd-box">
            Petugas Gudang,<br>
            <div class="ttd-name">{{ $barangKeluar->user->name ?? $barangKeluar->user->username ?? '-' }}</div>
        </div>
        <div class="ttd-box">
            Pihak Penerima,<br>
            <div class="ttd-name">{{ $barangKeluar->penerima ?? '-' }}</div>
        </div>
    </div>
</body>
</html>