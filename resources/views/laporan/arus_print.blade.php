<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Barang</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }
        .kop { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; }
        .kop h1 span { color: #000; }
        .kop p { margin: 5px 0 0 0; color: #000; font-size: 10pt;}
        h2 { text-align: center; font-size: 14pt; margin-bottom: 5px; color: #000; text-transform: uppercase; font-weight: bold; }
        .periode { text-align: center; margin-top: 0; margin-bottom: 20px; font-size: 10pt; color: #000; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px 5px; text-align: center; font-size: 9.5pt;}
        .table-data th { background-color: #fff; color: #000; text-transform: uppercase; font-size: 9pt; font-weight: bold; }
        .table-data td.text-left { text-align: left; padding-left: 10px; }
        
        .badge-masuk { border: 1px solid #000; color: #000; padding: 2px 5px; border-radius: 4px; font-weight: bold; font-size: 8pt;}
        .badge-keluar { border: 1px solid #f97316; color: #f97316; padding: 2px 5px; border-radius: 4px; font-weight: bold; font-size: 8pt;}
        
        .filters { font-size: 9pt; background-color: #fff; padding: 12px; border-radius: 0; border: 1px solid #000; color: #000; }
        .filters ul { margin: 5px 0 0 20px; padding: 0; color: #000; }
        .footer { text-align: right; margin-top: 30px; font-size: 9pt; font-style: italic; color: #000; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>RAK<span>SAKTI</span></h1>
        <p>Laporan Resmi Arus Pergerakan Barang</p>
    </div>

    <h2>Laporan Arus Barang ({{ $nama }})</h2>
    <p class="periode">Periode Transaksi: <strong>{{ $tanggalAwal }}</strong> s/d <strong>{{ $tanggalAkhir }}</strong></p>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 8%;">Aktivitas</th>
                <th style="width: 12%;">Kode</th>
                <th style="width: 25%;">Nama Produk</th>
                <th style="width: 10%;">Jumlah</th>
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 18%;">Pihak Terkait</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($row['jenis'] == 'Masuk')
                            <span class="badge-masuk">IN</span>
                        @else
                            <span class="badge-keluar">OUT</span>
                        @endif
                    </td>
                    <td>{{ $row['kode_barang'] }}</td>
                    <td class="text-left"><strong>{{ $row['nama_barang'] }}</strong></td>
                    <td style="font-weight: bold; {{ $row['jenis'] == 'Masuk' ? 'color: #000;' : 'color: #f97316;' }}">
                        {{ $row['jenis'] == 'Masuk' ? '+' : '-' }}{{ $row['jumlah'] }}
                    </td>
                    <td>{{ $row['lokasi'] ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($row['pihak'] ?? '-', 20) }}</td>
                </tr>
            @endforeach
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

    <div class="footer">
        Dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong>
    </div>
</body>
</html>