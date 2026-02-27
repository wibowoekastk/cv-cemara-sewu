<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi: Satu jenis obat bisa punya banyak batch (stok masuk berkali-kali).
     */
    public function batches()
    {
        return $this->hasMany(ObatBatch::class);
    }

    /**
     * Akses Cepat: Total Stok
     * Cara panggil di view: $obat->total_stok
     */
    public function getTotalStokAttribute()
    {
        // Hanya hitung batch yang statusnya 'active' dan stoknya > 0
        return $this->batches()
            ->where('status', 'active')
            ->sum('stok_saat_ini');
    }
}