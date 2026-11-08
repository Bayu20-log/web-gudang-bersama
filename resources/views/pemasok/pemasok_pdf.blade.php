<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Pasokan Pemasok</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }

        /* --- KOP SURAT FORMAL DINAMIS --- */
        .kop-surat-container { width: 100%; border-bottom: 3px solid #000; margin-bottom: 2px; padding-bottom: 6px; } 
        .kop-surat-wrapper { border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 15px; } 
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 15%; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 75px; height: 75px; object-fit: cover; border-radius: 50%; } 
        .kop-text { width: 70%; text-align: center; vertical-align: middle; }
        .kop-text h1 { margin: 0; font-size: 22pt; color: #f97316; letter-spacing: 1px; font-weight: bold; text-transform: uppercase; }
        .kop-text h3 { margin: 3px 0; font-size: 11pt; color: #000; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 2px 0; color: #000; font-size: 9pt; }
        .kop-spacer { width: 15%; }

        /* --- JUDUL LAPORAN --- */
        .judul { text-align: center; font-size: 13pt; font-weight: bold; text-decoration: underline; margin-bottom: 4px; text-transform: uppercase; color: #000; }
        .periode { text-align: center; font-size: 9.5pt; margin-bottom: 20px; color: #333; }

        /* --- TABEL DATA --- */
        table.table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px 6px; font-size: 9pt; vertical-align: middle; }
        .table-data th { background-color: #f2f2f2; color: #000; font-weight: bold; text-transform: uppercase; text-align: center; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .sub-text { font-size: 7.5pt; color: #555; display: block; margin-top: 2px; }
        .badge-count { color: #0d6efd; font-weight: bold; }
        .nominal-text { color: #198754; font-weight: bold; }

        /* --- FOOTER --- */
        .footer { margin-top: 30px; font-size: 8pt; color: #555; border-top: 1px dashed #aaa; padding-top: 6px; }
    </style>
</head>
<body>

    <!-- KOP SURAT DINAMIS -->
    <div class="kop-surat-wrapper">
        <div class="kop-surat-container">
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if(Auth::check() && Auth::user()->photo && file_exists(public_path('storage/' . Auth::user()->photo)))
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

    <!-- JUDUL LAPORAN -->
    <div class="judul">LAPORAN DETAIL PASOKAN PEMASOK</div>
    <div class="periode">Periode Data: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</div>

    <!-- TABEL DATA PASOKAN -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 28%;">NAMA PEMASOK</th>
                <th style="width: 22%;">KONTAK / PIC</th>
                <th style="width: 15%;">FREKUENSI TRANSAKSI</th>
                <th style="width: 15%;">TOTAL BARANG MASUK</th>
                <th style="width: 15%;">TOTAL NOMINAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemasoks as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ is_array($row) ? $row['nama_pemasok'] : $row->nama_pemasok }}</strong>
                    <span class="sub-text">{{ is_array($row) ? ($row['email'] ?? '-') : ($row->email ?? '-') }}</span>
                </td>
                <td>
                    {{ is_array($row) ? ($row['pic'] ?? '-') : ($row->pic ?? '-') }}
                    <span class="sub-text">{{ is_array($row) ? ($row['no_hp'] ?? '-') : ($row->no_hp ?? '-') }}</span>
                </td>
                <td class="text-center">
                    <span class="badge-count">{{ is_array($row) ? $row['frekuensi'] : $row->frekuensi }} Kali</span>
                </td>
                <td class="text-center">
                    <strong>{{ number_format(is_array($row) ? $row['total_barang_masuk'] : $row->total_barang_masuk, 0, ',', '.') }} Unit</strong>
                </td>
                <td class="text-right">
                    <span class="nominal-text">Rp {{ number_format(is_array($row) ? $row['total_nominal'] : $row->total_nominal, 0, ',', '.') }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data pasokan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p>Dicetak oleh <strong>{{ Auth::check() ? Auth::user()->name : 'Gudang' }}</strong> pada {{ date('d/m/Y H:i:s') }}</p>
    </div>

</body>
</html>