<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
    ];

    // Relasi ke Siklus
    public function siklus()
    {
        return $this->hasMany(Siklus::class);
    }
}