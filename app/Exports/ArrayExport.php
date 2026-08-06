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


namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ArrayExport implements FromArray, WithHeadings
{
    protected $data;


    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function array(): array
    {
        return $this->data;
    }


   public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Harga Dasar',  // ✅ tambah ini
            'Total Masuk',
            'Total Keluar',
            'Stok Akhir',
            'Lokasi',
            'Username',
        ];
}

}
