<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObatUsage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tahu batch mana yang dipakai
    public function batch()
    {
        return $this->belongsTo(ObatBatch::class, 'obat_batch_id');
    }
}