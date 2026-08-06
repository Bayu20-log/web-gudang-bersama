<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label Barang Masuk</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 20mm;
    }


    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }


    .label-container {
      border: 1px solid #000;
      padding: 20px;
      width: fit-content;
    }


    table.layout {
      width: 100%;
      border-collapse: collapse;
    }


    td.qr-col {
      width: 220px;
      vertical-align: top;
      text-align: center;
    }


    td.info-col {
      padding-left: 30px;
      vertical-align: top;
    }


    .info-table {
      font-size: 14px;
    }


    .info-table td {
      padding: 4px 8px;
    }


    .info-table td.label {
      font-weight: bold;
      width: 120px;
    }


    .qr-img {
      width: 200px;
      height: 200px;
      object-fit: contain;
      border: 1px solid #aaa;
      padding: 10px;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="label-container">
    <table class="layout">
      <tr>
        <td class="qr-col">
          @if($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code))
            <img class="qr-img" src="{{ public_path('storage/' . $barangMasuk->qr_code) }}" alt="QR Code">
          @else
            <em>Tidak tersedia</em>
          @endif
        </td>
        <td class="info-col">
          <table class="info-table">
            <tr><td class="label">Nama Barang</td><td>: {{ $barangMasuk->item->nama_barang ?? '-' }}</td></tr>
            <tr><td class="label">Jumlah</td><td>: {{ $barangMasuk->jumlah }}</td></tr>
            <tr><td class="label">Tanggal Masuk</td><td>: {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y') }}</td></tr>
            <tr><td class="label">Kadaluarsa</td><td>: {{ \Carbon\Carbon::parse($barangMasuk->tanggal_kadaluarsa)->format('d-m-Y') ?? '-' }}</td></tr>
            <tr><td class="label">Pemasok</td><td>: {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}</td></tr>
            <tr><td class="label">Lokasi</td><td>: {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</td></tr>
            <tr><td class="label">Kondisi</td><td>: {{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}</td></tr>
            <tr><td class="label">Catatan</td><td>: {{ $barangMasuk->catatan ?? '-' }}</td></tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
