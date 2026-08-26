<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stiker QR Code</title>
    <style>
        @page { margin: 5mm; }
        body { text-align: center; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; color: #000; }
        
        /* Frame batas stiker */
        .wrapper { border: 2px solid #000; padding: 10px; box-sizing: border-box; width: 100%; display: table; }
        
        .brand { font-size: 14pt; font-weight: bold; margin-bottom: 5px; color: #f97316; letter-spacing: 1px;}
        .brand span { color: #000; }
        
        .qr-container { margin: 5px 0; }
        img { width: 140px; height: 140px; }
        
        .kode { font-size: 16pt; font-weight: bold; margin-top: 5px; letter-spacing: 1px;}
        .nama { font-size: 11pt; font-weight: bold; text-transform: uppercase; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;}
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="brand">RAK<span>SAKTI</span></div>
        
        <div class="qr-container">
            @if($qrBase64)
                <img src="{{ $qrBase64 }}" alt="QR Code">
            @else
                <div style="height: 140px; border: 1px solid #000; display:flex; align-items:center; justify-content:center;">TIDAK ADA QR</div>
            @endif
        </div>
        
        <div class="kode">{{ $barangMasuk->kode_barang }}</div>
        <div class="nama">{{ \Illuminate\Support\Str::limit($barangMasuk->item->nama_barang ?? '-', 25) }}</div>
    </div>
</body>
</html>