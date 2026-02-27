<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPakanStock extends Model
{
    use HasFactory;

    protected $table = 'unit_pakan_stocks';

    protected $fillable = [
        'unit_id',
        'pakan_id',
        'jumlah_stok' // Stok fisik di Unit tersebut
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function pakan()
    {
        return $this->belongsTo(Pakan::class);
    }
}