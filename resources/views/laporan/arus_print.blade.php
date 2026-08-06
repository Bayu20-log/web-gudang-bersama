<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.center { text-align: center; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; }
        .footer-info { font-style: italic; font-size: 11px; margin-top: 20px; text-align: right; }
    </style>
</head>
<body>




    <h2>Laporan Arus Barang ({{ $nama }})</h2>
    <p class="center">Periode {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>




    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Dasar</th>
                <th>Jumlah</th>
                <th>Lokasi</th>
                <th>Pihak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                    <td>{{ $row['jenis'] }}</td>
                    <td>{{ $row['kode_barang'] }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td>Rp {{ number_format($row['harga_dasar']) }}</td>
                    <td>{{ $row['jumlah'] }}</td>
                    <td>{{ $row['lokasi'] ?? '-' }}</td>
                    <td>{{ $row['pihak'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>




    <br>
    <p><strong>Berdasarkan kriteria:</strong></p>
    <ul>
        @foreach($filters as $label => $value)
            @if($value)
                <li>{{ $label }}: {{ $value }}</li>
            @endif
        @endforeach
    </ul>




    <p class="footer-info">
        Laporan ini dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong>
    </p>




</body>
</html>
