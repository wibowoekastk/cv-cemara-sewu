<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObatBatch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke Master Obat
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}