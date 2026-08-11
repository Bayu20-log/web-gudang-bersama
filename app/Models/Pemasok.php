<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pemasok extends Model
{
    use HasFactory;


    protected $fillable = [
        'nama_pemasok',
        'email',
        'alamat',
        'no_telepon',
        'jenis',
        'bergabung_sejak',
        'nama_pic',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function barangMasuks()
    {
        return $this->hasMany(\App\Models\BarangMasuk::class, 'id_pemasok');
    }

    public function barangKeluars()
    {
        return $this->hasMany(\App\Models\BarangKeluar::class, 'id_pemasok');
    }


    // Jika ingin otomatis cast ke tanggal
    // protected $casts = [
    //     'bergabung_sejak' => 'date',
    // ];
}
