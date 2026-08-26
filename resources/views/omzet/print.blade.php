<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Omzet Barang</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }
        .kop { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; }
        .kop h1 span { color: #000; }
        .kop p { margin: 5px 0 0 0; color: #000; font-size: 10pt;}
        h2 { text-align: center; font-size: 14pt; margin-bottom: 5px; color: #000; text-transform: uppercase; font-weight: bold; }
        .periode { text-align: center; margin-top: 0; margin-bottom: 20px; font-size: 10pt; color: #000; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px 5px; text-align: center; }
        .table-data th { background-color: #fff; color: #000; text-transform: uppercase; font-size: 9pt; font-weight: bold; }
        .total-row td { font-weight: bold; background-color: #fff; text-transform: uppercase;}
        
        .filters { font-size: 9pt; background-color: #fff; padding: 12px; border: 1px solid #000; color: #000; }
        .filters ul { margin: 5px 0 0 20px; padding: 0; color: #000; }
        .footer-info { text-align: right; margin-top: 30px; font-size: 9pt; font-style: italic; color: #000; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>RAK<span>SAKTI</span></h1>
        <p>Laporan Resmi Omzet Penjualan Produk</p>
    </div>

    <h2>Laporan Omzet Barang ({{ $nama }})</h2>
    <p class="periode">Periode Transaksi: <strong>{{ $tanggalAwal }}</strong> s/d <strong>{{ $tanggalAkhir }}</strong></p>

    <table class="table-data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Produk</th>
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
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y H:i') }}</td>
                    <td style="text-align: left;"><strong>{{ $row['nama_barang'] }}</strong></td>
                    <td>{{ $row['lokasi'] }}</td>
                    <td>{{ $row['kondisi'] }}</td>
                    <td>{{ $row['jumlah_keluar'] }}</td>
                    <td>Rp {{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                    <td style="font-weight: bold; color: #f97316;">Rp {{ number_format($row['omzet_item'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align: right; padding-right: 15px;">Total Omzet Penjualan</td>
                <td style="color: #f97316;">Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="filters">
        <strong>Filter Diterapkan:</strong>
        <ul>
            @foreach($filters as $label => $value)
                @if($value)
                    <li>{{ $label }}: <strong>{{ $value }}</strong></li>
                @endif
            @endforeach
        </ul>
    </div>

    <p class="footer-info">
        Dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong>
    </p>
</body>
</html>