<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siklus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siklus';

    protected $fillable = [
        'kandang_id',
        'batch_id', // [PENTING] Kolom relasi ke Batch
        'tanggal_chick_in',
        'jenis_ayam',
        'populasi_awal',
        'umur_awal_minggu',
        'harga_satuan',
        'vendor_bibit',
        'status',
        'tanggal_selesai',
        'total_afkir',
        'catatan'
    ];

    protected $casts = [
        'tanggal_chick_in' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // --- RELASI PENTING (YANG MEMPERBAIKI ERROR) ---
    
    // Relasi ke Master Batch (Induk Angkatan)
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // Relasi ke Kandang
    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    // Relasi ke Daily Records (Produksi Harian)
    public function dailyRecords()
    {
        return $this->hasMany(DailyRecord::class);
    }

    // Helper: Menghitung Umur Ayam Saat Ini (dalam Minggu)
    public function getUmurSekarangAttribute()
    {
        if ($this->status !== 'Aktif' && $this->tanggal_selesai) {
            // Jika sudah afkir, hitung umur sampai tanggal selesai
            $selisihHari = $this->tanggal_chick_in->diffInDays($this->tanggal_selesai);
        } else {
            // Jika masih aktif, hitung sampai hari ini
            $selisihHari = $this->tanggal_chick_in->diffInDays(now());
        }

        $mingguBerjalan = floor($selisihHari / 7);
        return $this->umur_awal_minggu + $mingguBerjalan;
    }
}