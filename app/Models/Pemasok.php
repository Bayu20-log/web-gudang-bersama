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
    ];
    


    // Jika ingin otomatis cast ke tanggal
    // protected $casts = [
    //     'bergabung_sejak' => 'date',
    // ];
}
