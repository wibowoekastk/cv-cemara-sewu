<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timbangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kandang_id', 
        'user_id', 
        'tanggal_timbang', 
        'umur_minggu', 
        'berat_rata', 
        'uniformity', 
        'keterangan'
    ];

    // Relasi ke Kandang
    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    // Relasi ke User (Penginput)
    public function user()
    {
        return $this->belongsTo(User::class); // Pastikan Model User ada
    }
}