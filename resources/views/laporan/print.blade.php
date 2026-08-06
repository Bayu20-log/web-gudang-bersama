<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        p.cetak, p.periode, p.footer {
            text-align: center;
            margin: 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        ul {
            margin: 10px 0 0 20px;
            font-size: 11px;
        }
        .footer-info {
            margin-top: 15px;
            font-size: 11px;
            text-align: right;
        }
    </style>
</head>
<body>




    <h2>Laporan Stok Barang ({{ $nama }})</h2>
    <p class="periode">Periode {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>




    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga Dasar</th>
                <th>Total Masuk</th>
                <th>Total Keluar</th>
                <th>Stok Akhir</th>
                <th>Lokasi</th>
                <th>Username</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                @if (is_array($item) && array_key_exists('kode_barang', $item))
                    <tr>
                        <td>{{ $item['kode_barang'] }}</td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['total_masuk']) }}</td>
                        <td>{{ number_format($item['total_keluar']) }}</td>
                        <td>{{ number_format($item['stok_akhir']) }}</td>
                        <td>{{ $item['lokasi'] }}</td>
                        <td>{{ $item['username'] }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>




    {{-- Filter Aktif --}}
    <br>
    <p><strong>Berdasarkan kriteria:</strong></p>
    <ul>
        @foreach($filters as $label => $value)
            @if($value)
                <li>{{ $label }}: {{ $value }}</li>
            @endif
        @endforeach
    </ul>




    <p class="footer-info"><i>Laporan ini dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong></i></p>




</body>
</html>
