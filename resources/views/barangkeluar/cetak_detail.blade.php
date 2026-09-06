<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Transaksi Barang Keluar</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #000; }
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

        .judul { text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px; color: #000; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { text-align: left; padding: 10px; vertical-align: middle; border-bottom: 1px solid #000; color: #000; }
        th { width: 35%; font-weight: bold; background-color: #fff; }
        .highlight { font-size: 14pt; font-weight: bold; color: #f97316; }
        .footer-note { text-align: center; margin-top: 40px; font-size: 10pt; color: #000; font-style: italic; }
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

    <div class="judul">Ringkasan Transaksi Keluar</div>

    @php
        $hargaRata = \App\Models\BarangMasuk::where('kode_barang', $barangKeluar->kode_barang)
            ->where('id_lokasi', $barangKeluar->id_lokasi)
            ->where('id_kondisi', $barangKeluar->id_kondisi)
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->value('harga_satuan') ?? 0;
    @endphp

    <table>
        <tr><th>ID Transaksi</th><td><strong>TRX-OUT-{{ str_pad($barangKeluar->id, 5, '0', STR_PAD_LEFT) }}</strong></td></tr>
        <tr><th>Tanggal Keluar</th><td>{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y, H:i') }}</td></tr>
        <tr><th>Kode Produk</th><td>{{ $barangKeluar->kode_barang }}</td></tr>
        <tr><th>Nama Produk</th><td><strong>{{ $barangKeluar->item->nama_barang ?? '-' }}</strong></td></tr>
        <tr><th>Kondisi Produk</th><td>{{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}</td></tr>
        <tr><th>Lokasi Asal</th><td>{{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}</td></tr>
        <tr><th>Penerima</th><td>{{ $barangKeluar->penerima ?? '-' }}</td></tr>
        <tr><th>Tujuan Pengiriman</th><td>{{ $barangKeluar->lokasi_tujuan ?? '-' }}</td></tr>
        <tr><th>Petugas Pencatat</th><td>{{ $barangKeluar->user->name ?? $barangKeluar->user->username ?? '-' }}</td></tr>
        <tr><th>Catatan</th><td>{{ $barangKeluar->catatan ?? '-' }}</td></tr>
    </table>

    <table style="border: 3px solid #000; margin-top: 25px;">
        <tr>
            <th style="background-color: #fff; color: #000; width: auto; border-bottom: 1px solid #000;">Jumlah Keluar</th>
            <td style="text-align: right; font-size: 12pt; border-bottom: 1px solid #000;"><strong>{{ $barangKeluar->jumlah_keluar }}</strong> {{ $barangKeluar->item->satuan->nama_satuan ?? 'Unit' }}</td>
        </tr>
        <tr>
            <th style="background-color: #fff; color: #000; width: auto; border-bottom: 1px solid #000;">Harga Jual per Unit</th>
            <td style="text-align: right; border-bottom: 1px solid #000;">Rp {{ number_format($barangKeluar->harga_jual, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th style="background-color: #fff; color: #000; width: auto;">Total Transaksi</th>
            <td style="text-align: right;" class="highlight">Rp {{ number_format($barangKeluar->total_harga_jual, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini dihasilkan secara otomatis oleh sistem RAKSAKTI pada {{ now()->translatedFormat('d F Y H:i') }}.
    </div>
</body>
</html>