<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class Notification extends Model
{
    use HasFactory;


    // Jika nama tabel sesuai konvensi, baris ini tidak wajib
    // protected $table = 'notifications';




    protected $fillable = ['message', 'type', 'is_read', 'user_id'];




    protected $casts = [
        'is_read' => 'boolean',
    ];
}
