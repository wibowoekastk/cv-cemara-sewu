<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakanMutation extends Model
{
    use HasFactory;

    protected $table = 'pakan_mutations';

    protected $fillable = [
        'pakan_id',
        'user_id',
        'dari_unit_id',  // PENTING: Lokasi Asal (Untuk Pemakaian)
        'ke_unit_id',    // PENTING: Lokasi Tujuan (Untuk Distribusi)
        'kandang_id',
        'tanggal',
        'jenis_mutasi',
        'jumlah',
        'status',
        'keterangan'
    ];

    public function pakan()
    {
        return $this->belongsTo(Pakan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relasi Unit Asal (Wajib ada untuk Laporan Pemakaian)
    public function dariUnit()
    {
        return $this->belongsTo(Unit::class, 'dari_unit_id');
    }

    // Alias agar kompatibel
    public function unitAsal()
    {
        return $this->belongsTo(Unit::class, 'dari_unit_id');
    }

    // Relasi Unit Tujuan (Wajib ada untuk Laporan Distribusi)
    public function unitTujuan()
    {
        return $this->belongsTo(Unit::class, 'ke_unit_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }
}