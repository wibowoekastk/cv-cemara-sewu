<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'production_targets';

    protected $fillable = [
        'lokasi_id',
        'unit_id',
        'start_date',
        'end_date',
        'hd',
        'egg_weight',
        'fcr',          
        'bw',
        'mortality',
        'status',
    ];

    /**
     * Relasi ke Model Unit (Wajib ada untuk mengatasi RelationNotFoundException)
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Helper untuk mendapatkan target aktif
     */
    public static function getActiveTarget($unitId)
    {
        return self::where('unit_id', $unitId)
                   ->where('status', 'active')
                   ->whereDate('end_date', '>=', now())
                   ->latest()
                   ->first();
    }

    /**
     * Helper atribut lokasi (Opsional, untuk mempermudah tampilan di view)
     */
    public function getLokasiAttribute()
    {
        // Fallback jika relasi unit->lokasi belum ada
        return match($this->lokasi_id) {
            1 => 'Kalirambut',
            2 => 'Sokawangi',
            default => 'Lokasi ' . $this->lokasi_id,
        };
    }
}