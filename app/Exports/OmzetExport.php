<?php




namespace App\Exports;




use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;




class OmzetExport implements FromCollection, WithHeadings
{
    protected $request;




    public function __construct(Request $request)
    {
        $this->request = $request;
    }




    public function collection()
    {
        // Ambil data dengan relasi + filter
        $data = BarangKeluar::with(['item', 'lokasi', 'kondisi'])
            ->when($this->request->lokasi, fn($q) => $q->where('id_lokasi', $this->request->lokasi))
            ->when($this->request->kondisi, fn($q) => $q->where('id_kondisi', $this->request->kondisi))
            ->when($this->request->nama_barang, fn($q) =>
                $q->whereHas('item', fn($q2) => $q2->where('nama_barang', 'like', "%{$this->request->nama_barang}%"))
            )
            ->when($this->request->kode_barang, fn($q) =>
                $q->whereHas('item', fn($q2) => $q2->where('kode_barang', 'like', "%{$this->request->kode_barang}%"))
            )
            ->when($this->request->start_date && $this->request->end_date, fn($q) =>
                $q->whereBetween('tanggal_keluar', [$this->request->start_date, $this->request->end_date])
            )
            ->get();




        // Mapping data untuk Excel
        $mapped = $data->map(function ($row, $i) {
            // Format tanggal aman
            $tanggal = '-';
            if (!empty($row->tanggal_keluar)) {
                try {
                    $tanggal = Carbon::parse($row->tanggal_keluar)->format('d/m/Y H:i:s');
                } catch (\Exception $e) {
                    $tanggal = '-';
                }
            }




            return [
                'No'             => $i + 1,
                'Nama Barang'    => optional($row->item)->nama_barang ?? '-',
                'Tanggal'        => $tanggal,
                'Lokasi'         => optional($row->lokasi)->nama_lokasi ?? '-',
                'Kondisi'        => optional($row->kondisi)->nama_kondisi ?? '-',
                'Jumlah Keluar'  => $row->jumlah_keluar,
                'Harga Jual'     => $row->harga_jual,
                'Omzet'          => $row->jumlah_keluar * $row->harga_jual,
            ];
        });




        // Hitung total omzet
        $totalOmzet = $mapped->sum('Omzet');




        // Tambahkan baris total di akhir
        $mapped->push([
            'No'             => '',
            'Nama Barang'    => '',
            'Tanggal'        => '',
            'Lokasi'         => '',
            'Kondisi'        => '',
            'Jumlah Keluar'  => '',
            'Harga Jual'     => 'Total Omzet',
            'Omzet'          => $totalOmzet,
        ]);




        return $mapped;
    }




    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Tanggal',
            'Lokasi',
            'Kondisi',
            'Jumlah Keluar',
            'Harga Jual',
            'Omzet',
        ];
    }
}
