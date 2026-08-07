<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Satuan extends Model
{
    use HasFactory;


    protected $fillable = [
        'nama_satuan',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(Item::class, 'id_satuan');
    }

}
