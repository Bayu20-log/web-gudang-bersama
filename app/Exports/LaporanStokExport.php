<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Item;

class LaporanStokExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
{
    $start = $this->request->start_date;
    $end = $this->request->end_date;
    $kategori = $this->request->kategori;
    $lokasi = $this->request->lokasi;

    $items = Item::with([
        'kategori',
        'satuan',
        'barangMasuk.lokasi',
        'barangMasuk.user',
        'barangKeluar'
    ])
    ->when($kategori, fn($q) => $q->where('id_kategori', $kategori))
    ->when($lokasi, fn($q) => $q->whereHas('barangMasuk', fn($q2) => $q2->where('id_lokasi', $lokasi)))
    ->get();

    return $items->map(function ($item) {
    $totalMasuk = $item->barangMasuk->sum('jumlah');
    $totalKeluar = $item->barangKeluar->sum('jumlah_keluar');
    $stokAkhir = $totalMasuk - $totalKeluar;

    $lokasi = $item->barangMasuk->first()?->lokasi->nama_lokasi ?? '-';
    $user = $item->barangMasuk->first()?->user->username ?? '-';

    return [
        'kode_barang'   => $item->kode_barang,
        'nama_barang'   => $item->nama_barang,
        'harga_dasar'   => $item->harga_dasar ?? 0, // ✅ pastikan ada default
        'kategori'      => $item->kategori->kategori ?? '-',
        'satuan'        => $item->satuan->nama_satuan ?? '-',
        'total_masuk'   => $totalMasuk,
        'total_keluar'  => $totalKeluar,
        'stok_akhir'    => $stokAkhir,
        'lokasi'        => $lokasi,
        'username'      => $user,
    ];
});

}

public function headings(): array
{
    return [
        'Kode Barang',
        'Nama Barang',
        'Harga Dasar',   // urutan 3
        'Kategori',
        'Satuan',
        'Total Masuk',
        'Total Keluar',
        'Stok Akhir',
        'Lokasi',
        'Username',
    ];
}


}
