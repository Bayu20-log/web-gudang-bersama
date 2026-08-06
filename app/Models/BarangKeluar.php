<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BarangKeluar extends Model
{
    use HasFactory;


    protected $fillable = [
        'kode_barang',
        'id_lokasi',
        'id_kondisi',
        'jumlah_keluar',
        'harga_jual',
        'total_harga_jual',
        'tanggal_keluar',
        'user_id',
        'tujuan_pengeluaran',
        'penerima',
        'lokasi_tujuan',
        'catatan',
    ];


    /**
     * Relasi ke model Item berdasarkan kode_barang (bukan id)
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');
    }


    /**
     * Relasi ke model Lokasi
     */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
   
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }
    

    

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
