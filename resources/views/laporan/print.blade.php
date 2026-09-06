<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }

        /* --- KOP SURAT FORMAL --- */
        /* Padding dan margin diperkecil untuk merapatkan jarak tulisan dengan garis bawah */
        .kop-surat-container { width: 100%; border-bottom: 3px solid #000; margin-bottom: 2px; padding-bottom: 6px; } 
        .kop-surat-wrapper { border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 15px; } 
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 15%; text-align: left; vertical-align: middle; }
        
        /* Foto dibuat bulat dan proporsional */
        .kop-logo img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; } 
        
        .kop-text { width: 70%; text-align: center; vertical-align: middle; }
        .kop-text h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; text-transform: uppercase; }
        .kop-text h3 { margin: 3px 0; font-size: 12pt; color: #000; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 2px 0; color: #000; font-size: 10pt; }
        .kop-spacer { width: 15%; }

        h2 { text-align: center; font-size: 14pt; margin-bottom: 5px; color: #000; text-transform: uppercase; font-weight: bold; }
        .periode { text-align: center; margin-top: 0; margin-bottom: 20px; font-size: 10pt; color: #000; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px 5px; text-align: center; }
        .table-data th { background-color: #fff; color: #000; text-transform: uppercase; font-size: 9pt; font-weight: bold; }
        .table-data td.text-left { text-align: left; padding-left: 10px; }
        
        .filters { font-size: 9pt; background-color: #fff; padding: 12px; border: 1px solid #000; color: #000; }
        .filters ul { margin: 5px 0 0 20px; padding: 0; color: #000; }
        .footer { text-align: right; margin-top: 30px; font-size: 9pt; font-style: italic; color: #000; }
    </style>
</head>
<body>

    <!-- KOP SURAT BARU -->
    <div class="kop-surat-wrapper">
        <div class="kop-surat-container">
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        <!-- Memanggil foto profil user, fallback ke logo default jika belum ada -->
                        @if(Auth::check() && Auth::user()->photo)
                            <img src="{{ public_path('storage/' . Auth::user()->photo) }}" alt="Foto Profil">
                        @else
                            <img src="{{ public_path('images/logo_or.png') }}" alt="Logo Default">
                        @endif
                    </td>
                    <td class="kop-text">
                        <!-- Mengambil nama toko dan detail kontak dari profil user -->
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

    <h2>Laporan Stok Barang</h2>
    <p class="periode">Periode Data: <strong>{{ $tanggalAwal }}</strong> s/d <strong>{{ $tanggalAkhir }}</strong></p>
    
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 10%;">Kode</th>
                <th style="width: 25%;">Nama Produk</th>
                <th style="width: 15%;">Harga Dasar</th>
                <th style="width: 10%;">Masuk</th>
                <th style="width: 10%;">Keluar</th>
                <th style="width: 10%;">Stok Akhir</th>
                <th style="width: 10%;">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                @if (is_array($item) && array_key_exists('kode_barang', $item))
                    <tr>
                        <td>{{ $item['kode_barang'] }}</td>
                        <td class="text-left"><strong>{{ $item['nama_barang'] }}</strong></td>
                        <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                        <td style="color: #000; font-weight: bold;">+{{ number_format($item['total_masuk']) }}</td>
                        <td style="color: #f97316; font-weight: bold;">-{{ number_format($item['total_keluar']) }}</td>
                        <td style="font-weight: bold; font-size: 11pt;">{{ number_format($item['stok_akhir']) }}</td>
                        <td>{{ $item['lokasi'] }}</td>
                    </tr>
                @endif
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