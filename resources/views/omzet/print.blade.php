



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Omzet Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.center { text-align: center; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; }
        .total-row { font-weight: bold; background: #f9f871; }
        .footer-info { font-style: italic; font-size: 11px; margin-top: 20px;text-align: right }
    </style>
</head>
<body>




    <h2>Laporan Omzet Barang ({{ $nama }})</h2>
    {{-- Perbaiki tampilan periode agar sesuai dengan yang dikirim --}}
    <p class="center">Periode: {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>




    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Jumlah Keluar</th>
                <th>Harga Jual</th>
                <th>Omzet Item</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    {{-- Format ulang tanggal agar sesuai permintaan dd/mm/yyyy --}}
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td>{{ $row['lokasi'] }}</td>
                    <td>{{ $row['kondisi'] }}</td>
                    <td>{{ $row['jumlah_keluar'] }}</td>
                    <td>Rp {{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($row['omzet_item'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6">Total Omzet</td>
                <td>Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>




    <br>
    <p><strong>Berdasarkan kriteria:</strong></p>
    <ul>
        @foreach($filters as $label => $value)
            {{-- Pastikan hanya filter yang memiliki nilai yang ditampilkan --}}
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
