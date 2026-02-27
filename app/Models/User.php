<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Removed to fix error

class User extends Authenticatable
{
    use HasFactory, Notifiable; // Removed HasApiTokens trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',     // Pastikan role ada
        'unit_id',  // <--- WAJIB DITAMBAHKAN agar bisa simpan unit
        'lokasi_id', // Menambahkan lokasi_id agar bisa disimpan
        'avatar',
        'status',    // Menambahkan status (active/inactive)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- RELASI KE UNIT ---
    // Ini yang membuat $user->unit->nama_unit bisa jalan di View
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}