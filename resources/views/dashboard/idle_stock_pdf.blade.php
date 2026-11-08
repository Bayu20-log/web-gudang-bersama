<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Idle Stock</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 10pt; 
            color: #000; 
        }

        /* --- KOP SURAT FORMAL BAYU --- */
        .kop-surat-container { width: 100%; padding-bottom: 6px; } 
        .kop-surat-wrapper { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; } 
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 15%; text-align: left; vertical-align: middle; }
        
        /* Foto profil bulat & proporsional gaya Bayu */
        .kop-logo img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; } 
        
        .kop-text { width: 70%; text-align: center; vertical-align: middle; }
        .kop-text h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; text-transform: uppercase; }
        .kop-text h3 { margin: 3px 0; font-size: 12pt; color: #000; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 2px 0; color: #000; font-size: 10pt; }
        .kop-spacer { width: 15%; }

        /* --- JUDUL LAPORAN GAYA BAYU --- */
        .judul { 
            text-align: center; 
            font-size: 14pt; 
            font-weight: bold; 
            text-decoration: underline; 
            margin-bottom: 5px; 
            text-transform: uppercase; 
            color: #000; 
        }
        .nomor { 
            text-align: center; 
            font-size: 10.5pt; 
            margin-bottom: 20px; 
            color: #000; 
        }

        /* --- TABEL DATA BORDER HITAM FORMAL BAYU --- */
        table { width: 100%; border-collapse: collapse; }
        .table-data { margin-top: 15px; margin-bottom: 20px; }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 9px 8px; 
            text-align: center; 
            vertical-align: middle; 
        }
        .table-data th { 
            background-color: #fff; 
            color: #000; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 10pt; 
        }

        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }

        /* --- BADGE STATUS --- */
        .badge { 
            padding: 3px 8px; 
            font-weight: bold; 
            border-radius: 4px; 
            font-size: 8.5pt; 
            display: inline-block; 
        }
        .badge-idle { background-color: #ffebe9; color: #d9381e; border: 1px solid #d9381e; }
        .badge-perhatian { background-color: #fff8e6; color: #d97706; border: 1px solid #d97706; }
        .badge-aktif { background-color: #ecfdf5; color: #059669; border: 1px solid #059669; }

        /* --- TANDA TANGAN FORMAL BAYU --- */
        .ttd-container { width: 100%; margin-top: 40px; text-align: center; }
        .ttd-box { width: 48%; display: inline-block; vertical-align: top; }
        .ttd-name { margin-top: 75px; font-weight: bold; text-decoration: underline; color: #000; }
    </style>
</head>
<body>

    <!-- KOP SURAT FORMAL BAYU -->
    <div class="kop-surat-wrapper">
        <div class="kop-surat-container">
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if(Auth::check() && Auth::user()->photo && file_exists(public_path('storage/' . Auth::user()->photo)))
                            <img src="{{ public_path('storage/' . Auth::user()->photo) }}" alt="Foto Profil">
                        @elseif(Auth::check() && Auth::user()->photo && file_exists(public_path(Auth::user()->photo)))
                            <img src="{{ public_path(Auth::user()->photo) }}" alt="Foto Profil">
                        @else
                            <img src="{{ public_path('images/logo_or.png') }}" alt="Logo Default">
                        @endif
                    </td>
                    <td class="kop-text">
                        <h1>{{ Auth::check() ? strtoupper(Auth::user()->nama_toko ?? 'RAKSAKTI') : 'RAKSAKTI' }}</h1>
                        <h3>Sistem Informasi Manajemen Gudang & Logistik Terpadu</h3>
                        <p>{{ Auth::check() ? (Auth::user()->alamat_toko ?? 'Jl. Soekarno Hatta KM.15') : 'Jl. Soekarno Hatta KM.15' }}</p>
                        <p>
                            Email: {{ Auth::check() ? Auth::user()->email : 'admin@gmail.com' }} | 
                            Telp: {{ (Auth::check() && Auth::user()->phone) ? Auth::user()->phone : '081142389833' }}
                        </p>
                    </td>
                    <td class="kop-spacer"></td>
                </tr>
            </table>
        </div>
    </div>
    <!-- END KOP SURAT -->

    <!-- JUDUL LAPORAN -->
    <div class="judul">LAPORAN IDLE STOCK (BARANG TIDAK BERGERAK)</div>
    <div class="nomor">
        Monitoring Ketersediaan Barang &bull; Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }}</strong>
    </div>

    <p style="margin-bottom: 10px;">
        Berikut adalah rincian persediaan barang berdasarkan durasi tidak mengalami transaksi pengeluaran (Idle Stock):
    </p>

    <!-- TABEL DATA BORDER HITAM FORMAL -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 40%;">NAMA BARANG</th>
                <th style="width: 12%;">STOK</th>
                <th style="width: 18%;">TERAKHIR KELUAR</th>
                <th style="width: 12%;">TIDAK BERGERAK</th>
                <th style="width: 13%;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            @php
                $hari = $item['hari_idle_num'] ?? 0;
                if ($hari >= 90) {
                    $statusText = 'Idle';
                    $badgeClass = 'badge-idle';
                } elseif ($hari >= 30) {
                    $statusText = 'Perlu Perhatian';
                    $badgeClass = 'badge-perhatian';
                } else {
                    $statusText = 'Aktif';
                    $badgeClass = 'badge-aktif';
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">
                    <strong>{{ $item['nama_barang'] ?? '-' }}</strong>
                    <div style="font-size: 8pt; color: #555;">Kode: {{ $item['kode_barang'] ?? '-' }}</div>
                </td>
                <td class="text-center"><strong>{{ number_format($item['stok'] ?? 0) }}</strong></td>
                <td class="text-center">{{ $item['terakhir_bergerak'] ?? '-' }}</td>
                <td class="text-center"><strong>{{ $hari }} hari</strong></td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; font-style: italic;">
                    Tidak ada data barang idle.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top: 10px; font-size: 9pt;">
        <em>* Total Barang Terdata: <strong>{{ count($items) }}</strong> item. Laporan ini diproduksi otomatis sebagai acuan evaluasi rotasi persediaan barang.</em>
    </p>

    <!-- TANDA TANGAN FORMAL BAYU -->
    <div class="ttd-container">
        <div class="ttd-box">
            Mengetahui,<br>
            Kepala Gudang / Supervisor
            <div class="ttd-name">( .................................... )</div>
        </div>
        <div class="ttd-box">
            Petugas Gudang,<br>
            <div class="ttd-name">{{ $printedBy ?? (Auth::check() ? Auth::user()->name : 'Petugas Gudang') }}</div>
        </div>
    </div>

</body>
</html>