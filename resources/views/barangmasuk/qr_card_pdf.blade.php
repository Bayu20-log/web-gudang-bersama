<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label Inventaris</title>
  <style>
    @page { size: A4 landscape; margin: 15mm; }
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; color: #000; }
    
    .label-box { border: 4px solid #000; padding: 20px; width: 100%; max-width: 950px; margin: 0 auto; box-sizing: border-box; }
    .label-header { border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 15px; display: table; width: 100%; }
    
    .header-left { display: table-cell; vertical-align: middle; }
    .header-left h1 { margin: 0; font-size: 26pt; color: #f97316; letter-spacing: 1px; font-weight: bold; }
    .header-left h1 span { color: #000; }
    
    .header-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 20pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;}
    
    table.layout { width: 100%; border-collapse: collapse; }
    
    td.photo-col { width: 25%; text-align: center; vertical-align: middle; border-right: 2px solid #000; padding-right: 15px; }
    .photo-img { max-width: 100%; max-height: 180px; object-fit: contain; border: 1px solid #000; padding: 5px; }
    
    td.info-col { width: 50%; vertical-align: top; padding: 0 20px; }
    .info-table { width: 100%; font-size: 12pt; border-collapse: collapse; }
    .info-table td { padding: 8px 0; border-bottom: 1px dashed #000; vertical-align: top;}
    .info-table td.label { font-weight: bold; width: 35%; text-transform: uppercase; font-size: 10pt; }
    .info-value { font-weight: bold; font-size: 14pt; text-transform: uppercase; }
    
    td.qr-col { width: 25%; vertical-align: middle; text-align: center; border-left: 2px solid #000; padding-left: 15px; }
    .qr-img { width: 180px; height: 180px; }
    .qr-text { margin-top: 10px; font-size: 11pt; font-weight: bold; text-align: center; border-top: 2px solid #000; padding-top: 5px;}
  </style>
</head>
<body>
  <div class="label-box">
    <div class="label-header">
        <div class="header-left">
            <h1>RAK<span>SAKTI</span></h1>
        </div>
        <div class="header-right">
            LABEL INVENTARIS
        </div>
    </div>
    <table class="layout">
      <tr>
        <!-- Kolom Kiri: Foto -->
        <td class="photo-col">
          @if($barangMasuk->item->foto && Storage::disk('public')->exists($barangMasuk->item->foto))
            <img class="photo-img" src="{{ public_path('storage/' . $barangMasuk->item->foto) }}" alt="Foto">
          @else
            <div style="border: 2px dashed #000; height: 150px; display:flex; align-items:center; justify-content:center;">
                <em style="font-size: 12pt;">TANPA FOTO</em>
            </div>
          @endif
        </td>
        
        <!-- Kolom Tengah: Info Detail -->
        <td class="info-col">
          <table class="info-table">
            <tr>
                <td class="label">Kode Produk</td>
                <td class="info-value">: {{ $barangMasuk->kode_barang }}</td>
            </tr>
            <tr>
                <td class="label">Nama Produk</td>
                <td class="info-value">: {{ $barangMasuk->item->nama_barang ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tgl Masuk</td>
                <td style="font-size: 12pt; font-weight: bold;">: {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Isi</td>
                <td style="font-size: 14pt;">: <strong>{{ $barangMasuk->jumlah }}</strong> {{ $barangMasuk->item->satuan->nama_satuan ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi / Rak</td>
                <td style="font-size: 12pt; font-weight: bold;">: {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none;" class="label">Kondisi</td>
                <td style="border: none; font-size: 12pt; font-weight: bold;">: {{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}</td>
            </tr>
          </table>
        </td>
        
        <!-- Kolom Kanan: QR Code -->
        <td class="qr-col">
          @if($qrBase64)
            <img class="qr-img" src="{{ $qrBase64 }}" alt="QR Code">
          @else
            <div style="height: 180px; border: 1px solid #000;">QR KOSONG</div>
          @endif
          <div class="qr-text">SCAN UNTUK DETAIL</div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>