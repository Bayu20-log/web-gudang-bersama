<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Prediksi - {{ $prediksi->item->nama_barang ?? '-' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #1a1a1a;
        }

        /* ===== KOP / LETTERHEAD ===== */
        .kop {
            width: 100%;
            border-bottom: 3px double #111827;
            padding-bottom: 10px;
            margin-bottom: 4px;
        }
        .kop table { border: none; margin: 0; }
        .kop td { border: none; padding: 0; text-align: left; }
        .kop .kop-sistem {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #111827;
        }
        .kop .kop-tagline {
            font-size: 10.5px;
            color: #4b5563;
            font-style: italic;
        }
        .kop .kop-doknomor {
            text-align: right;
            font-size: 10.5px;
            color: #374151;
            line-height: 1.6;
        }

        h1.judul-laporan {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 16px 0 2px;
        }
        p.subtitle {
            text-align: center;
            margin: 0 0 2px;
            font-size: 12px;
            font-weight: bold;
        }
        p.periode {
            text-align: center;
            margin: 0 0 18px;
            font-size: 10.5px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #111827;
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background-color: #e5e7eb;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-top: 24px;
            margin-bottom: 2px;
            text-transform: uppercase;
            border-bottom: 1px solid #111827;
            padding-bottom: 3px;
        }
        .info-table th {
            width: 220px;
            text-align: left;
            background-color: #f3f4f6;
        }
        .info-table td { text-align: left; }

        .closing-text {
            margin-top: 22px;
            font-size: 11.5px;
            line-height: 1.7;
            text-align: justify;
        }

        .signature-block {
            width: 100%;
            margin-top: 30px;
            border: none;
        }
        .signature-block td {
            border: none;
            text-align: center;
            font-size: 11px;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-block .sig-place-date { margin-bottom: 55px; }
        .signature-block .sig-line {
            border-top: 1px solid #111827;
            margin-top: 5px;
            padding-top: 4px;
            font-weight: bold;
        }
        .signature-block .sig-role {
            font-size: 10px;
            color: #4b5563;
        }

        .footer-info {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            border-top: 1px solid #9ca3af;
            padding-top: 4px;
            font-size: 9.5px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ===================== KOP / LETTERHEAD ===================== --}}
    <div class="kop">
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="kop-sistem">RAKSAKTI</div>
                    <div class="kop-tagline">Sistem Manajemen Stok Digital untuk Usaha Mikro, Kecil, dan Menengah (UMKM)</div>
                </td>
                <td class="kop-doknomor" style="width: 35%;">
                    No. Laporan: RAK/PRD/{{ str_pad($prediksi->id, 4, '0', STR_PAD_LEFT) }}/{{ now()->format('m/Y') }}<br>
                    Tanggal Cetak: {{ now()->format('d M Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <h1 class="judul-laporan">Laporan Hasil Prediksi Kebutuhan Barang</h1>
    <p class="subtitle">{{ $prediksi->item->kode_barang }} - {{ $prediksi->item->nama_barang }}</p>
    <p class="periode">Modul Prediksi Barang Keluar &bull; RAKSAKTI</p>

    {{-- ================= 1. INFORMASI UMUM (termasuk Tingkat Akurasi) ================= --}}
    @php
        $mapeValue = $selectedResult->mase_atau_wape ?? null;
        $akurasiLabel = null;
        $akurasiDesc = null;
        $akurasiBg = null;
        $akurasiText = null;

        if ($mapeValue !== null) {
            if ($mapeValue < 10) {
                $akurasiLabel = 'Sangat Akurat';
                $akurasiDesc = 'Prediksi ini sangat bisa diandalkan rata-rata melesetnya kecil sekali.';
                $akurasiBg = '#d1fae5';
                $akurasiText = '#065f46';
            } elseif ($mapeValue < 20) {
                $akurasiLabel = 'Akurat';
                $akurasiDesc = 'Prediksi ini cukup bisa diandalkan untuk dijadikan acuan stok.';
                $akurasiBg = '#dbeafe';
                $akurasiText = '#1e40af';
            } elseif ($mapeValue < 50) {
                $akurasiLabel = 'Cukup Akurat';
                $akurasiDesc = 'Prediksi ini masih wajar dipakai, tapi sebaiknya tetap dicek ulang berkala.';
                $akurasiBg = '#fef3c7';
                $akurasiText = '#92400e';
            } else {
                $akurasiLabel = 'Tidak Akurat';
                $akurasiDesc = 'Prediksi ini kurang bisa diandalkan, jangan dijadikan satu-satunya acuan.';
                $akurasiBg = '#fee2e2';
                $akurasiText = '#991b1b';
            }
        }
    @endphpz
    <p class="section-title">1. Informasi Prediksi</p>
    <table class="info-table">
        <tr>
            <th>Item</th>
            <td>{{ $prediksi->item->kode_barang }} - {{ $prediksi->item->nama_barang }}</td>
        </tr>
        <tr>
            <th>Periode Data Historis</th>
            <td>{{ \Carbon\Carbon::parse($prediksi->data_start)->format('d M Y') }}  {{ \Carbon\Carbon::parse($prediksi->data_end)->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Frekuensi</th>
            <td>{{ ucfirst($prediksi->frequency) }}</td>
        </tr>
        <tr>
            <th>Horizon Prediksi</th>
            <td>{{ $prediksi->horizon }} hari ke depan</td>
        </tr>
        <tr>
            <th>Model Terpilih</th>
            <td>{{ $prediksi->selected_model }}</td>
        </tr>
        <tr>
            <th>Metrik Seleksi</th>
            <td>{{ $prediksi->selection_metric }}</td>
        </tr>
        @if($akurasiLabel)
        <tr>
            <th>Tingkat Akurasi</th>
            <td style="background-color: {{ $akurasiBg }}; color: {{ $akurasiText }};">
                <strong>{{ $akurasiLabel }}</strong> (MAPE {{ number_format($mapeValue, 1) }}%) {{ $akurasiDesc }}
            </td>
        </tr>
        @endif
    </table>

    {{-- ================= 2. RINCIAN HASIL PREDIKSI ================= --}}
    <p class="section-title">2. Rincian Hasil Prediksi ({{ $prediksi->selected_model }})</p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Langkah</th>
                <th>Prediksi Kebutuhan</th>
                <th>Actual</th>
                <th>Selisih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($forecastValues as $fv)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fv->forecast_date)->format('d M Y') }}</td>
                    <td>t+{{ $fv->horizon_step }}</td>
                    <td>{{ number_format($fv->predicted_requirement, 2) }}</td>
                    <td>{{ $fv->actual_quantity_out !== null ? number_format($fv->actual_quantity_out, 2) : '-' }}</td>
                    <td>
                        @if($fv->actual_quantity_out !== null)
                            @php $selisih = $fv->actual_quantity_out - $fv->predicted_requirement; @endphp
                            {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 2) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ================= 4. RINGKASAN HASIL PREDIKSI ================= --}}
    @php
        $totalPrediksi = $forecastValues->sum('predicted_requirement');
        $rataRata = $forecastValues->count() > 0 ? $totalPrediksi / $forecastValues->count() : 0;
        $tanggalMulai = $forecastValues->min('forecast_date');
        $tanggalSelesai = $forecastValues->max('forecast_date');
    @endphp
    <p class="section-title">3. Ringkasan Hasil Prediksi</p>
    <table>
        <thead>
            <tr>
                <th>Total Kebutuhan (unit)</th>
                <th>Rata-rata per Hari</th>
                <th>Periode Prediksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($totalPrediksi, 0, ',', '.') }}</td>
                <td>{{ number_format($rataRata, 1, ',', '.') }}</td>
                <td>
                    @if($tanggalMulai && $tanggalSelesai)
                        {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ================= PENUTUP ================= --}}
    <p class="closing-text">
        Demikian laporan hasil prediksi kebutuhan barang ini disusun secara otomatis oleh sistem
        RAKSAKTI berdasarkan data historis transaksi barang keluar yang tersedia pada basis data.
        Laporan ini dapat dijadikan salah satu acuan dalam pengambilan keputusan pengadaan dan
        pengelolaan stok, dengan tetap mempertimbangkan kondisi dan kebijakan usaha yang berlaku.
    </p>

    <table class="signature-block">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="sig-place-date">Dicetak, {{ now()->format('d M Y') }}</div>
                <div class="sig-line">Sistem RAKSAKTI</div>
                <div class="sig-role">Dokumen digenerate otomatis oleh sistem</div>
            </td>
        </tr>
    </table>

    <div class="footer-info">
        Dokumen ini dihasilkan secara otomatis oleh sistem RAKSAKTI dan sah tanpa memerlukan tanda tangan basah.
    </div>

</body>
</html>