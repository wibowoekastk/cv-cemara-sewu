<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama_unit', 'lokasi'];

    // Relasi: Satu Unit punya banyak Kandang
    public function kandangs()
    {
        return $this->hasMany(Kandang::class);
    }
    
    // Relasi: Satu Unit punya banyak Stok Pakan
    public function pakanStocks()
    {
        return $this->hasMany(UnitPakanStock::class);
    }
}