<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Total Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 0; }
        .periode { text-align: center; margin-top: 2px; margin-bottom: 15px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; }
        .total-row { font-weight: bold; background-color: #e0e0e0; }
        .footer-info {
            margin-top: 20px;
            font-size: 11px;
            text-align: right;
        }
    </style>
</head>
<body>
















    <h2>Laporan Total Aset ({{ $nama }})</h2>
    <p class="periode">Periode {{ $tanggalAwal }} sd {{ $tanggalAkhir }}</p>
















    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Stok Akhir</th>
                <th>Harga Beli</th>
                <th>Jumlah Aset</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                @if ($row['harga_beli'] === 'Total Aset')
                    <tr class="total-row">
                        <td colspan="5">Total Aset</td>
                        <td>Rp {{ number_format($row['jumlah_aset'], 0, ',', '.') }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $row['nama_barang'] }}</td>
                        <td>{{ $row['lokasi'] }}</td>
                        <td>{{ $row['kondisi'] }}</td>
                        <td>{{ $row['stok_akhir'] }}</td>
                        <td>Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row['jumlah_aset'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
   
    <br>
    <p><strong>Berdasarkan kriteria:</strong></p>
    <ul style="font-size: 12px;">
        @foreach($filters as $label => $value)
            @if($value && !in_array($label, ['Tanggal Mulai', 'Tanggal Selesai']) || ($label === 'Tanggal Mulai' && request('start_date')) || ($label === 'Tanggal Selesai' && request('end_date')))
                <li>{{ $label }}: {{ $value }}</li>
            @endif
        @endforeach
    </ul>














    <p class="footer-info">
        <i>Laporan ini dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong></i>
    </p>
















</body>
</html>
