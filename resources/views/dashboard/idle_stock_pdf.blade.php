<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Idle Stock</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9pt; 
            color: #2d3748; 
            line-height: 1.4;
        }

        /* --- KOP SURAT --- */
        .kop-surat-wrapper { 
            border-bottom: 2px solid #1a202c; 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
        } 
        .kop-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .kop-logo { 
            width: 12%; 
            text-align: left; 
            vertical-align: middle; 
        }
        .kop-logo img { 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
        } 
        .kop-text { 
            width: 76%; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-text h1 { 
            margin: 0; 
            font-size: 16pt; 
            color: #ea580c; 
            letter-spacing: 0.5px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .kop-text h3 { 
            margin: 2px 0; 
            font-size: 9pt; 
            color: #4a5568; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .kop-text p { 
            margin: 1px 0; 
            color: #718096; 
            font-size: 8pt; 
        }
        .kop-spacer { width: 12%; }

        /* --- JUDUL LAPORAN --- */
        .judul-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul { 
            font-size: 12pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #1a202c; 
            letter-spacing: 0.5px;
        }
        .sub-judul { 
            font-size: 8.5pt; 
            color: #718096; 
            margin-top: 3px;
        }

        /* --- TABEL DATA --- */
        table.table-data { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .table-data th { 
            background-color: #f7fafc; 
            color: #4a5568; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 8pt; 
            letter-spacing: 0.5px;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #cbd5e0;
            padding: 8px;
        }
        .table-data td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 8px; 
            font-size: 8.5pt; 
            color: #2d3748;
            vertical-align: middle; 
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* --- BADGE STATUS --- */
        .badge { 
            padding: 3px 8px; 
            font-weight: bold; 
            border-radius: 12px; 
            font-size: 7.5pt; 
            display: inline-block; 
        }
        .badge-idle { background-color: #fee2e2; color: #dc2626; }
        .badge-perhatian { background-color: #fef3c7; color: #d97706; }
        .badge-aktif { background-color: #d1fae5; color: #059669; }

        /* --- FOOTER --- */
        .footer { 
            margin-top: 20px; 
            font-size: 7.5pt; 
            color: #a0aec0; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 6px; 
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat-wrapper">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if(Auth::check() && Auth::user()->photo && file_exists(public_path('storage/' . Auth::user()->photo)))
                        <img src="{{ public_path('storage/' . Auth::user()->photo) }}" alt="Logo">
                    @else
                        <img src="{{ public_path('images/logo_or.png') }}" alt="Logo">
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

    <!-- JUDUL LAPORAN -->
    <div class="judul-container">
        <div class="judul">Laporan Idle Stock</div>
        <div class="sub-judul">
            Monitoring Persediaan Barang Berdasarkan Durasi Tidak Bergerak &bull; Tanggal Cetak: {{ date('d/m/Y H:i') }}
        </div>
    </div>

    <!-- TABEL DATA -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 6%;" class="text-center">NO</th>
                <th style="width: 44%;">NAMA BARANG</th>
                <th style="width: 12%;" class="text-center">STOK</th>
                <th style="width: 18%;" class="text-center">TERAKHIR KELUAR</th>
                <th style="width: 10%;" class="text-center">TIDAK BERGERAK</th>
                <th style="width: 10%;" class="text-center">STATUS</th>
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
                <td class="text-center" style="color: #718096;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item['nama_barang'] ?? '-' }}</strong>
                    <div style="font-size: 7.5pt; color: #a0aec0;">Kode: {{ $item['kode_barang'] ?? '-' }}</div>
                </td>
                <td class="text-center"><strong>{{ number_format($item['stok'] ?? 0) }}</strong></td>
                <td class="text-center" style="color: #4a5568;">{{ $item['terakhir_bergerak'] ?? '-' }}</td>
                <td class="text-center"><strong>{{ $hari }} hari</strong></td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #a0aec0;">
                    Tidak ada data barang idle.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td>Dicetak oleh: <strong>{{ $printedBy }}</strong></td>
                <td class="text-right">Total Barang: <strong>{{ count($items) }}</strong></td>
            </tr>
        </table>
    </div>

</body>
</html>