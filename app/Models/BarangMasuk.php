<?php




namespace App\Models;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class BarangMasuk extends Model
{
    use HasFactory;




    protected $fillable = [
        'kode_barang',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'id_pemasok',
        'id_lokasi',
        'id_kondisi',
        'qr_code',
        'user_id',
        'catatan',
    ];




    // Relasi ke model Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'kode_barang', 'kode_barang');
    }




    // Relasi ke model Pemasok
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }




    // Relasi ke model Lokasi
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }




    // Relasi ke model Kondisi
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'id_kondisi');
    }




    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
