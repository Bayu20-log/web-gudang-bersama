<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Barang</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 8px;
        }
        img {
            width: 250px;
            height: 250px;
        }
        .kode {
            margin-top: 8px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if($qrBase64)
    <img src="{{ $qrBase64 }}" alt="QR Code" >
    @else
        <p>QR Code tidak tersedia</p>
    @endif
    <div class="kode">{{ $barangMasuk->kode_barang }}</div>
</body>
</html>
