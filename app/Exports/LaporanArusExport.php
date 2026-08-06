<?php


namespace App\Exports;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class LaporanArusExport implements FromCollection, WithHeadings
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
        $search = $this->request->search;
        $userId = Auth::id();


        // Barang Masuk
        $arusMasuk = BarangMasuk::with(['item.kategori', 'lokasi', 'pemasok'])
            ->where('user_id', $userId)
            ->when($start && $end, fn($q) => $q->whereBetween('tanggal_masuk', [$start, $end]))
            ->get()
            ->filter(function ($masuk) use ($kategori, $lokasi, $search) {
                return (!$kategori || $masuk->item->id_kategori == $kategori)
                    && (!$lokasi || $masuk->id_lokasi == $lokasi)
                    && (!$search || str_contains(strtolower($masuk->item->nama_barang), strtolower($search))
                        || str_contains(strtolower($masuk->item->kode_barang), strtolower($search))
                        || str_contains(strtolower($masuk->pemasok?->nama_pemasok ?? ''), strtolower($search)));
            })
            ->map(function ($masuk) {
                return [
                    'tanggal' => Carbon::parse($masuk->tanggal_masuk)->format('d/m/Y H:i:s'),
                    'jenis' => 'Masuk',
                    'kode_barang' => $masuk->item->kode_barang ?? '-',
                    'nama_barang' => $masuk->item->nama_barang ?? '-',
                    'harga_dasar' => $masuk->item->harga_dasar ?? 0,
                    'jumlah' => $masuk->jumlah,
                    'lokasi' => $masuk->lokasi->nama_lokasi ?? '-',
                    'pihak' => $masuk->pemasok->nama_pemasok ?? '-',
                ];
            });


        // Barang Keluar
        $arusKeluar = BarangKeluar::with(['item.kategori', 'lokasi'])
            ->where('user_id', $userId)
            ->when($start && $end, fn($q) => $q->whereBetween('tanggal_keluar', [$start, $end]))
            ->get()
            ->filter(function ($keluar) use ($kategori, $lokasi, $search) {
                return (!$kategori || $keluar->item->id_kategori == $kategori)
                    && (!$lokasi || $keluar->id_lokasi == $lokasi)
                    && (!$search || str_contains(strtolower($keluar->item->nama_barang), strtolower($search))
                        || str_contains(strtolower($keluar->item->kode_barang), strtolower($search))
                        || str_contains(strtolower($keluar->penerima ?? ''), strtolower($search)));
            })
            ->map(function ($keluar) {
                return [
                    'tanggal' => Carbon::parse($keluar->tanggal_keluar)->format('d/m/Y H:i:s'),
                    'jenis' => 'Keluar',
                    'kode_barang' => $keluar->item->kode_barang ?? '-',
                    'nama_barang' => $keluar->item->nama_barang ?? '-',
                    'harga_dasar' => $keluar->item->harga_dasar ?? 0,
                    'jumlah' => $keluar->jumlah_keluar,
                    'lokasi' => $keluar->lokasi->nama_lokasi ?? '-',
                    'pihak' => $keluar->penerima ?? '-',
                ];
            });


        // Gabungkan & urutkan
        return $arusMasuk->merge($arusKeluar)
            ->sortBy('tanggal')
            ->values();
    }


    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Kode Barang',
            'Nama Barang',
            'Harga Dasar',  // ✅ tambah heading
            'Jumlah',
            'Lokasi',
            'Pihak',
        ];
    }

}
