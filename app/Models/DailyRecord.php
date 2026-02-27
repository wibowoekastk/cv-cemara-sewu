<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRecord extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Kandang
    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    // Relasi ke Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi ke User (Pembuat)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Pakan
    public function pakan()
    {
        return $this->belongsTo(Pakan::class);
    }

    // --- RELASI BARU (SIKLUS/BATCH) ---
    // Menghubungkan input harian ke siklus tertentu
    public function siklus()
    {
        return $this->belongsTo(Siklus::class);
    }

    // ==========================================
    // KOLOM VIRTUAL (ACCESSOR) - TIDAK PERLU DI DATABASE
    // ==========================================

    // Cara panggil: $record->hh_butir_harian
    public function getHhButirHarianAttribute()
    {
        // Ambil stok awal dari relasi kandang
        $stokAwal = $this->kandang->stok_awal ?? 1; // Hindari pembagian 0
        return $stokAwal > 0 ? ($this->telur_butir / $stokAwal) : 0;
    }

    // Cara panggil: $record->hh_kg_harian
    public function getHhKgHarianAttribute()
    {
        // Ambil stok awal dari relasi kandang
        $stokAwal = $this->kandang->stok_awal ?? 1;
        return $stokAwal > 0 ? ($this->telur_kg / $stokAwal) : 0;
    }
}