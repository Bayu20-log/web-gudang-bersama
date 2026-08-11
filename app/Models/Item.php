<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Pemasok;
use App\Models\Satuan;
use App\Models\User;
use App\Models\Kondisi;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'kode_barang'; // Jika kode_barang adalah PRIMARY
    public $incrementing = false; // Jika kode_barang berupa string/non-auto increment

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'foto',
        'id_kategori',
        'id_satuan',
        'stok_minimum',
         'harga_dasar',
         'user_id',
    ];



    // ✅ Barang Masuk berdasarkan kode_barang
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'kode_barang', 'kode_barang');
    }

    // ✅ Barang Keluar berdasarkan kode_barang
    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'kode_barang', 'kode_barang');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }

   

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    

    // ✅ Static helper untuk dropdown kategori
    public static function getKategoriOptions()
    {
        return DB::table('items')
            ->join('kategoris', 'items.id_kategori', '=', 'kategoris.id')
            ->select('kategoris.kategori')
            ->distinct()
            ->orderBy('kategoris.kategori')
            ->pluck('kategoris.kategori')
            ->toArray();
    }

    public static function generateKodeBarang()
    {
        $latest = self::orderBy('kode_barang', 'desc')->first();
        return $latest ? $latest->kode_barang + 1 : 1001; // mulai dari 1001
    }

    
}
