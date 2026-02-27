<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Units (Lokasi Farm)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit'); // Contoh: Unit Alpha
            $table->string('lokasi');    // Contoh: Kalirambut
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Kandangs (Master Kandang)
        Schema::create('kandangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->string('nama_kandang'); // Contoh: Kandang 01
            
            // Kapasitas & Populasi
            $table->integer('kapasitas'); 
            $table->integer('stok_awal')->default(0); 
            $table->integer('stok_saat_ini')->default(0); 
            
            $table->date('tgl_masuk')->nullable();      // Tanggal ayam masuk kandang
            $table->integer('umur_awal')->default(0);   // Umur ayam saat masuk (dalam minggu)
            
            // Status: 'aktif', 'kosong', 'persiapan'
            $table->string('status')->default('kosong'); 
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Timbangans (Rekap Bobot)
        Schema::create('timbangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandangs')->onDelete('cascade');
            // user_id bisa nullable jika belum ada sistem login user
            $table->foreignId('user_id')->nullable(); 
            
            $table->date('tanggal_timbang');
            $table->integer('umur_minggu'); // Disimpan manual atau hitung otomatis
            $table->float('berat_rata');    // Gram (misal: 1850.5)
            $table->float('uniformity');    // Persen (misal: 85.5)
            
            $table->text('keterangan')->nullable(); // Catatan tambahan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timbangans');
        Schema::dropIfExists('kandangs');
        Schema::dropIfExists('units');
    }
};