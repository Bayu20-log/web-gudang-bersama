<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Prioritas Tindakan Stok</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }

        /* --- KOP SURAT FORMAL --- */
        .kop-surat-container { width: 100%; padding-bottom: 6px; } 
        .kop-surat-wrapper { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; } 
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 15%; text-align: left; vertical-align: middle; }
        
        /* Foto bulat & proporsional */
        .kop-logo img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; } 
        
        .kop-text { width: 70%; text-align: center; vertical-align: middle; }
        .kop-text h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; text-transform: uppercase; }
        .kop-text h3 { margin: 3px 0; font-size: 12pt; color: #000; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 2px 0; color: #000; font-size: 10pt; }
        .kop-spacer { width: 15%; }

        /* --- JUDUL LAPORAN --- */
        .judul { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; color: #000; }
        .nomor { text-align: center; font-size: 10.5pt; margin-bottom: 25px; color: #000; }

        /* --- TABEL DATA STYLE --- */
        table { width: 100%; border-collapse: collapse; }
        .table-data { margin-top: 15px; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 9px 8px; text-align: center; vertical-align: middle; }
        .table-data th { background-color: #fff; color: #000; font-weight: bold; text-transform: uppercase; font-size: 10pt; }
        
        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }

        /* --- BADGE STATUS STOK --- */
        .badge { padding: 3px 8px; font-weight: bold; border-radius: 4px; font-size: 8.5pt; display: inline-block; }
        .badge-habis { background-color: #ffebe9; color: #d9381e; border: 1px solid #d9381e; }
        .badge-kritis { background-color: #fff8e6; color: #d97706; border: 1px solid #d97706; }
        .badge-rendah { background-color: #eff6ff; color: #2563eb; border: 1px solid #2563eb; }
        .badge-aman { background-color: #ecfdf5; color: #059669; border: 1px solid #059669; }

        /* --- TANDA TANGAN FORMAL --- */
        .ttd-container { width: 100%; margin-top: 40px; text-align: center; }
        .ttd-box { width: 48%; display: inline-block; vertical-align: top; }
        .ttd-name { margin-top: 75px; font-weight: bold; text-decoration: underline; color: #000; }
    </style>
</head>
<body>

    <!-- KOP SURAT FORMAL -->
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
                        <h1>{{ Auth::check() ? strtoupper(Auth::user()->nama_toko ?? 'NAMA TOKO') : 'NAMA TOKO' }}</h1>
                        <h3>Sistem Informasi Manajemen Gudang & Logistik Terpadu</h3>
                        <p>{{ Auth::check() ? (Auth::user()->alamat_toko ?? 'Alamat Toko Belum Diatur') : 'Alamat Toko Belum Diatur' }}</p>
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

    <!-- JUDUL -->
    <div class="judul">LAPORAN MONITORING PRIORITAS TINDAKAN STOK</div>
    <div class="nomor">
        Filter Status: <strong>{{ ucfirst($filterStatus) }}</strong> &bull; Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }}</strong>
    </div>

    <!-- TABEL DATA -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 6%;">NO</th>
                <th style="width: 18%;">KODE BARANG</th>
                <th style="width: 38%;">NAMA BARANG</th>
                <th style="width: 18%;">STATUS STOK</th>
                <th style="width: 20%;">KETERANGAN STOK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            @php
                $badgeClass = match($item['tag']) {
                    'Habis'  => 'badge-habis',
                    'Kritis' => 'badge-kritis',
                    'Rendah' => 'badge-rendah',
                    default  => 'badge-aman',
                };
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center"><strong>{{ $item['kode_barang'] }}</strong></td>
                <td class="text-left">{{ $item['nama'] }}</td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ $item['tag'] }}</span>
                </td>
                <td class="text-center">
                    Stok <strong>{{ $item['stok_akhir'] }}</strong> / Minimum {{ $item['stok_minimum'] }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 15px; font-style: italic;">
                    Tidak ada data prioritas tindakan untuk status ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top: 15px; font-size: 9pt;">
        <em>* Laporan ini diproduksi secara otomatis oleh sistem gudang sebagai acuan proses restok barang.</em>
    </p>

    <!-- TANDA TANGAN FORMAL -->
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