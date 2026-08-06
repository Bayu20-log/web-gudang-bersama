<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pengeluaran Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.5; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        .no-border td { border: none; padding: 3px 0; }
        .header { text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 10px; }
        .section { margin-top: 20px; }
        .text-left { text-align: left; }
    </style>
</head>
<body>


    <div class="header">BERITA ACARA PENGELUARAN BARANG</div>


    <p>
        Nomor : BA-KB/{{ $barangKeluar->id }}/{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('m') }}/{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('Y') }}
    </p>


    <p>
        Pada hari ini {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('l') }},
        tanggal {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y') }},
        bertempat di {{ $barangKeluar->lokasi->nama_lokasi }},
        telah dilakukan pengeluaran barang kepada:
    </p>


    <p><strong>Pihak Penerima:</strong></p>
    <table class="no-border" style="width: 70%;">
        <tr>
            <td style="width: 30%; text-align: left;">Nama</td>
            <td style="width: 5%;">:</td>
            <td style="text-align: left;">{{ $barangKeluar->penerima }}</td>
        </tr>
        <tr>
            <td style="text-align: left;">Unit Tujuan</td>
            <td>:</td>
            <td style="text-align: left;">{{ $barangKeluar->lokasi_tujuan }}</td>
        </tr>
    </table>


    <div class="section">
        <p>Adapun barang yang diserahkan adalah sebagai berikut:</p>


        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Tujuan</th>
                    <th>Tanggal Keluar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $barangKeluar->kode_barang }}</td>
                    <td>{{ $barangKeluar->item->nama_barang }}</td>
                    <td>{{ $barangKeluar->jumlah_keluar }}</td>
                    <td>{{ $barangKeluar->item->satuan->nama_satuan ?? '-' }}</td>
                    <td>{{ $barangKeluar->lokasi_tujuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') }}</td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="section">
        <p><strong>Catatan:</strong> {{ $barangKeluar->catatan ?? '-' }}</p>
    </div>


    <div class="section">
        <p>
            Demikian berita acara ini dibuat dengan sebenar-benarnya dan telah disetujui oleh pihak-pihak terkait.
        </p>
        <p style="margin-bottom: 0;">
            {{ $barangKeluar->lokasi->nama_lokasi }}, {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y') }}
        </p>
    </div>


    <table class="no-border" style="margin-top: 50px; text-align: center;">
        <tr>
            <td style="width: 50%;">
                Petugas Gudang<br><br><br><br>
                ( {{ $barangKeluar->user->username }} )
            </td>
            <td style="width: 50%;">
                Pihak Penerima<br><br><br><br>
                ( {{ $barangKeluar->penerima }} )
            </td>
        </tr>
    </table>


</body>
</html>
