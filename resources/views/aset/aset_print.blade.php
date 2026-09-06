<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Total Aset</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }

        /* --- KOP SURAT FORMAL --- */
        .kop-surat-container { width: 100%; border-bottom: 3px solid #000; margin-bottom: 2px; padding-bottom: 6px; } 
        .kop-surat-wrapper { border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 15px; } 
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 15%; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; } 
        .kop-text { width: 70%; text-align: center; vertical-align: middle; }
        .kop-text h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; text-transform: uppercase; }
        .kop-text h3 { margin: 3px 0; font-size: 12pt; color: #000; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 2px 0; color: #000; font-size: 10pt; }
        .kop-spacer { width: 15%; }

        h2 { text-align: center; font-size: 14pt; margin-bottom: 5px; color: #000; text-transform: uppercase; font-weight: bold; }
        .periode { text-align: center; margin-top: 0; margin-bottom: 20px; font-size: 10pt; color: #000; }
        
        /* --- TABEL DATA --- */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px; text-align: center; }
        .table-data th { background-color: #fff; color: #000; text-transform: uppercase; font-size: 10pt; font-weight: bold; }
        .table-data .total-row td { background-color: #fff; font-weight: bold; }
        
        /* --- FOOTER --- */
        .kriteria-title { font-weight: bold; font-size: 10pt; margin-bottom: 5px; margin-top: 30px; }
        .kriteria-list { font-size: 10pt; margin: 0; padding-left: 20px; }
        .footer-info { text-align: right; font-size: 10pt; font-style: italic; margin-top: 40px; }
    </style>
</head>
<body>

    <!-- KOP SURAT BARU -->
    <div class="kop-surat-wrapper">
        <div class="kop-surat-container">
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if(Auth::check() && Auth::user()->photo)
                            <img src="{{ public_path('storage/' . Auth::user()->photo) }}" alt="Foto Profil">
                        @else
                            <img src="{{ public_path('images/logo_or.png') }}" alt="Logo Default">
                        @endif
                    </td>
                    <td class="kop-text">
                        <h1>{{ Auth::check() ? strtoupper(Auth::user()->nama_toko) : 'NAMA TOKO' }}</h1>
                        <h3>Sistem Informasi Manajemen Gudang & Logistik Terpadu</h3>
                        <p>{{ Auth::check() ? Auth::user()->alamat_toko : 'Alamat Toko Belum Diatur' }}</p>
                        <p>
                            Email: {{ Auth::check() ? Auth::user()->email : '-' }} | 
                            Telp: {{ (Auth::check() && Auth::user()->phone) ? Auth::user()->phone : '-' }}
                        </p>
                    </td>
                    <td class="kop-spacer"></td>
                </tr>
            </table>
        </div>
    </div>
    <!-- END KOP SURAT -->
    
    <h2>Laporan Total Aset</h2>
    <p class="periode">Periode {{ $tanggalAwal }} sd {{ $tanggalAkhir }}</p>
    
    <!-- Penambahan class="table-data" agar CSS teraplikasikan -->
    <table class="table-data">
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
    
    <!-- Perbaikan struktur footer agar lebih rapi -->
    <div class="kriteria-title">Berdasarkan kriteria:</div>
    <ul class="kriteria-list">
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