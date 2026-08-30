@php
    // Warna badge berdasarkan level notifikasi (info/warning/critical)
    $levelColor = match ($level) {
        'info' => '#0d6efd',      // biru
        'warning' => '#fd7e14',   // oranye
        'critical' => '#dc3545',  // merah
        default => '#6c757d',     // abu-abu (fallback)
    };

    $levelLabel = match ($level) {
        'info' => 'INFO',
        'warning' => 'WARNING',
        'critical' => 'CRITICAL',
        default => strtoupper($level),
    };

    $typeLabel = match ($type) {
        'initial' => 'Peringatan Baru',
        'escalation' => 'Eskalasi',
        'resolution' => 'Resolusi',
        'reminder' => 'Pengingat',
        default => ucfirst($type),
    };
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">

                    {{-- Header dengan warna sesuai level --}}
                    <tr>
                        <td style="background-color:{{ $levelColor }}; padding:16px 24px;">
                            <span style="color:#ffffff; font-size:12px; font-weight:bold; letter-spacing:1px;">
                                {{ $levelLabel }} &middot; {{ $typeLabel }}
                            </span>
                        </td>
                    </tr>

                    {{-- Isi utama --}}
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 12px 0; color:#212529; font-size:20px;">
                                {{ $title }}
                            </h2>

                            <p style="margin:0 0 20px 0; color:#495057; font-size:14px; line-height:1.5;">
                                {{ $pesanNotifikasi }}
                            </p>

                            {{-- Tabel ringkasan barang --}}
                            <table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e9ecef; border-radius:4px; font-size:13px; color:#212529;">
                                <tr style="background-color:#f8f9fa;">
                                    <td style="border-bottom:1px solid #e9ecef;"><strong>Nama Barang</strong></td>
                                    <td style="border-bottom:1px solid #e9ecef;">{{ $namaBarang }}</td>
                                </tr>
                                <tr>
                                    <td style="border-bottom:1px solid #e9ecef;"><strong>Kode Barang</strong></td>
                                    <td style="border-bottom:1px solid #e9ecef;">{{ $kodeBarang }}</td>
                                </tr>
                                <tr style="background-color:#f8f9fa;">
                                    <td style="border-bottom:1px solid #e9ecef;"><strong>Stok Saat Ini</strong></td>
                                    <td style="border-bottom:1px solid #e9ecef;">{{ $stokAktual }}</td>
                                </tr>
                                @if($lowThreshold !== null)
                                <tr>
                                    <td style="border-bottom:1px solid #e9ecef;"><strong>Threshold Rendah</strong></td>
                                    <td style="border-bottom:1px solid #e9ecef;">{{ $lowThreshold }}</td>
                                </tr>
                                @endif
                                @if($criticalThreshold !== null)
                                <tr style="background-color:#f8f9fa;">
                                    <td><strong>Threshold Kritis</strong></td>
                                    <td>{{ $criticalThreshold }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:16px 24px; background-color:#f8f9fa; border-top:1px solid #e9ecef;">
                            <p style="margin:0; color:#adb5bd; font-size:11px;">
                                Email otomatis dari sistem RAKSAKTI Stock Monitoring. Jangan membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>