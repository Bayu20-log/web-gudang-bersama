<?php




namespace App\Models;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class Kategori extends Model
{
    use HasFactory;




    protected $fillable = ['kategori', 'deskripsi'];
    
    public function items()
{
    return $this->hasMany(\App\Models\Item::class, 'id_kategori');
}

}
