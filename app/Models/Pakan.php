<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pakan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pakans';

    protected $fillable = [
        'nama_pakan',
        'jenis_pakan',
        'satuan',
        'stok_pusat', // Stok fisik di Gudang Pusat (Admin)
        'min_stok',
        'deskripsi'
    ];

    // Relasi ke stok yang ada di unit-unit (Gudang Mandor)
    public function unitStocks()
    {
        return $this->hasMany(UnitPakanStock::class);
    }

    // Relasi ke semua riwayat mutasi (Masuk/Keluar/Transfer)
    // Menggantikan fungsi restocks() dan usages() yang lama
    public function mutations()
    {
        return $this->hasMany(PakanMutation::class);
    }
    
    // Helper: Hitung total aset pakan (Pusat + Semua Unit)
    public function getTotalStokGlobalAttribute()
    {
        return $this->stok_pusat + $this->unitStocks->sum('jumlah_stok');
    }
}
