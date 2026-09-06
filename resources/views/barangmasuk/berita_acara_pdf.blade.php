<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Penerimaan Barang</title>
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

        .judul { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; color: #000; }
        .nomor { text-align: center; font-size: 11pt; margin-bottom: 30px; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        .table-info td { padding: 4px 8px; border: none; vertical-align: top; }
        .table-data { margin-top: 15px; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 10px 8px; text-align: center; }
        .table-data th { background-color: #fff; color: #000; font-weight: bold; text-transform: uppercase; font-size: 10pt; }
        .ttd-container { width: 100%; margin-top: 50px; text-align: center; }
        .ttd-box { width: 48%; display: inline-block; }
        .ttd-name { margin-top: 80px; font-weight: bold; text-decoration: underline; color: #000;}
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

    <div class="judul">BERITA ACARA PENERIMAAN BARANG</div>
    <div class="nomor">Nomor : BA-PB/{{ str_pad($barangMasuk->id, 4, '0', STR_PAD_LEFT) }}/{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('m/Y') }}</div>

    <p style="text-align: justify;">
        Pada hari ini <strong>{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('l') }}</strong>, 
        tanggal <strong>{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }}</strong>, 
        bertempat di lokasi <strong>{{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</strong>, 
        telah dilakukan penerimaan barang dari:
    </p>

    <table class="table-info" style="width: 80%; margin-left: 20px;">
        <tr><td style="width: 25%;"><strong>Nama Pemasok</strong></td><td style="width: 3%;">:</td><td>{{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}</td></tr>
        <tr><td><strong>PIC Pemasok</strong></td><td>:</td><td>{{ $barangMasuk->pemasok->nama_pic ?? '-' }}</td></tr>
        <tr><td><strong>No. Telepon</strong></td><td>:</td><td>{{ $barangMasuk->pemasok->no_telepon ?? '-' }}</td></tr>
    </table>

    <p>Adapun rincian barang yang diterima adalah sebagai berikut:</p>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Kode Produk</th>
                <th style="width: 35%;">Nama Produk</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 25%;">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $barangMasuk->kode_barang }}</td>
                <td style="text-align: left;">{{ $barangMasuk->item->nama_barang ?? '-' }}</td>
                <td><strong>{{ $barangMasuk->jumlah }}</strong> {{ $barangMasuk->item->satuan->nama_satuan ?? '' }}</td>
                <td>{{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <p><strong>Catatan Tambahan:</strong><br>
    <span style="font-style: italic;">"{{ $barangMasuk->catatan ?: 'Tidak ada catatan khusus.' }}"</span></p>

    <p style="text-align: justify; margin-top: 30px;">
        Demikian berita acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd-container">
        <div class="ttd-box">
            Pihak Pengirim,<br>
            <div class="ttd-name">{{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}</div>
        </div>
        <div class="ttd-box">
            Petugas Gudang,<br>
            <div class="ttd-name">{{ $barangMasuk->user->name ?? '-' }}</div>
        </div>
    </div>
</body>
</html>