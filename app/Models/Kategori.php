<?php




namespace App\Models;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class Kategori extends Model
{
    use HasFactory;




    protected $fillable = ['kategori', 'deskripsi','user_id'];
    
        public function items()
    {
        return $this->hasMany(\App\Models\Item::class, 'id_kategori');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
