<?php








namespace App\Exports;








use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;








class LaporanAsetExport implements FromCollection, WithHeadings
{
    protected $request;








    public function __construct($request = null)
    {
        $this->request = $request;
    }




    private function parseDate($date, $startOfDay = true)
    {
        if (!$date) return null;


        try {
            $parsed = \Carbon\Carbon::parse($date);
            return $startOfDay ? $parsed->startOfDay() : $parsed->endOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }




    public function collection()
{
    $userId = auth()->id();


    $startDate = $this->parseDate($this->request->tanggal_mulai, true);
    $endDate   = $this->parseDate($this->request->tanggal_selesai, false);


    $items = Item::with(['barangMasuk.kondisi', 'barangMasuk.lokasi', 'barangKeluar'])
        ->whereHas('barangMasuk', function ($q) use ($userId, $startDate, $endDate) {
            $q->where('user_id', $userId);


            if ($startDate && $endDate) {
                $q->whereBetween('tanggal_masuk', [$startDate, $endDate]);
            }
        })
        ->get();


    $data = collect();


    foreach ($items as $item) {
        $filteredBM = $item->barangMasuk;


        if ($startDate && $endDate) {
            $filteredBM = $filteredBM->filter(function ($bm) use ($startDate, $endDate) {
                return \Carbon\Carbon::parse($bm->tanggal_masuk)->between($startDate, $endDate);
            });
        }


        $grup = $filteredBM->groupBy(function ($bm) {
            return $bm->item->nama_barang . '|' .
                   ($bm->lokasi->nama_lokasi ?? '-') . '|' .
                   ($bm->kondisi->nama_kondisi ?? '-');
        });


        foreach ($grup as $key => $groupedBM) {
            [$namaBarang, $lokasi, $kondisi] = explode('|', $key);
            $totalMasuk = $groupedBM->sum('jumlah');


            $totalKeluar = $item->barangKeluar
                ->filter(function ($bk) use ($lokasi, $kondisi, $startDate, $endDate) {
                    $validDate = true;
                    if ($startDate && $endDate) {
                        $validDate = \Carbon\Carbon::parse($bk->tanggal_keluar)->between($startDate, $endDate);
                    }


                    return $validDate &&
                        (($bk->lokasi->nama_lokasi ?? '-') === $lokasi) &&
                        (($bk->kondisi->nama_kondisi ?? '-') === $kondisi);
                })
                ->sum('jumlah_keluar');


            $stokAkhir = $totalMasuk - $totalKeluar;
            $hargaBeli = $groupedBM->first()?->harga_satuan ?? 0;


            $data->push([
                'nama_barang' => $namaBarang,
                'lokasi' => $lokasi,
                'kondisi' => $kondisi,
                'stok_akhir' => $stokAkhir,
                'harga_beli' => $hargaBeli,
                'jumlah_aset' => $stokAkhir * $hargaBeli,
            ]);
        }
    }


    $grouped = $data->groupBy(function ($item) {
        return $item['nama_barang'] . '|' . $item['lokasi'] . '|' . $item['kondisi'];
    })->map(function ($group) {
        $first = $group->first();
        $stok = $group->sum('stok_akhir');
        return [
            'nama_barang' => $first['nama_barang'],
            'lokasi' => $first['lokasi'],
            'kondisi' => $first['kondisi'],
            'stok_akhir' => $stok,
            'harga_beli' => $first['harga_beli'],
            'jumlah_aset' => $stok * $first['harga_beli'],
        ];
    })->values();


    // Apply request filters
    $filtered = $grouped->filter(function ($item) {
        $req = $this->request;
        return $item['stok_akhir'] > 0 &&
            (!$req->lokasi || $item['lokasi'] == $req->lokasi) &&
            (!$req->kondisi || $item['kondisi'] == $req->kondisi) &&
            (!$req->nama_barang || stripos($item['nama_barang'], $req->nama_barang) !== false);
    })->values();


    // Tambah total
    $total = $filtered->sum('jumlah_aset');
    $filtered->push([
        'nama_barang' => '',
        'lokasi' => '',
        'kondisi' => '',
        'stok_akhir' => '',
        'harga_beli' => 'Total Aset',
        'jumlah_aset' => $total,
    ]);


    return $filtered;
}






    public function headings(): array
    {
        return [
            'Nama Barang',
            'Lokasi',
            'Kondisi',
            'Stok Akhir',
            'Harga Beli',
            'Jumlah Aset',
        ];
    }
}
