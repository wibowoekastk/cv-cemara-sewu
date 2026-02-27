<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Kandang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kandangs';

    protected $fillable = [
        'unit_id', 
        'nama_kandang', 
        'kapasitas', 
        'stok_awal', 
        'stok_saat_ini', 
        
        // UPDATE DI SINI: Sesuaikan dengan kolom baru di migration
        'tgl_masuk',    // Tanggal masuk fisik
        'umur_awal',    // Umur saat masuk (minggu)
        
        'status'
    ];

    // --- Relasi Utama (Existing) ---

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function timbangans()
    {
        return $this->hasMany(Timbangan::class);
    }

    // --- Relasi BARU untuk Fitur Siklus (Batch) ---
    // Ditambahkan agar sistem siklus bisa berjalan

    // 1. Relasi ke Semua History Siklus (Untuk melihat riwayat batch sebelumnya)
    public function historySiklus()
    {
        return $this->hasMany(Siklus::class)->orderBy('tanggal_chick_in', 'desc');
    }

    // 2. Relasi KHUSUS untuk mengambil Siklus yang sedang AKTIF saja
    public function siklusAktif()
    {
        return $this->hasOne(Siklus::class)->where('status', 'Aktif')->latest();
    }

    // 3. Relasi ke Input Harian (Penting untuk backward compatibility)
    public function dailyRecords()
    {
        return $this->hasMany(DailyRecord::class);
    }

    // --- Logika & Helper (Existing) ---

    // UPDATE LOGIKA HITUNG UMUR OTOMATIS
    // Umur Sekarang = Umur Awal + (Selisih Hari dari Tgl Masuk sampai Hari Ini)
    public function getUmurMingguAttribute()
    {
        // Jika tidak ada tanggal masuk, kembalikan umur awal (atau 0)
        if (!$this->tgl_masuk) return $this->umur_awal;
        
        $tglMasuk = Carbon::parse($this->tgl_masuk);
        $hariIni = Carbon::now();
        
        // Hitung selisih minggu yang telah berlalu sejak masuk
        $selisihMinggu = (int) $tglMasuk->diffInWeeks($hariIni);
        
        // Total Umur
        return $this->umur_awal + $selisihMinggu;
    }

    public function getPersentaseIsiAttribute()
    {
        if ($this->kapasitas > 0) {
            return round(($this->stok_saat_ini / $this->kapasitas) * 100, 1);
        }
        return 0;
    }
}