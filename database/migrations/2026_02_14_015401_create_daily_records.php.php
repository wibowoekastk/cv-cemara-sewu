<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id();
            
            // --- A. Identitas Laporan & Relasi ---
            
            // Relasi ke Unit & Kandang
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('kandang_id')->constrained('kandangs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); 

            // [PENTING] Relasi ke Siklus/Batch
            // Karena file ini tanggal 15 Feb, dia aman memanggil tabel 'siklus' (yang tanggal 9 Feb)
            $table->foreignId('siklus_id')
                  ->nullable()
                  ->constrained('siklus')
                  ->onDelete('set null');

            $table->date('tanggal'); 
            
            // --- B. Data Populasi (Ayam) ---
            $table->integer('populasi_awal'); 
            $table->integer('mati')->default(0);
            $table->integer('afkir')->default(0); 
            
            // [BARU] Kolom Keterangan Kematian (Untuk Tooltip)
            $table->string('ket_mati')->nullable()->comment('Penyebab kematian ayam');
            
            // --- C. Data Produksi Telur ---
            $table->integer('telur_butir')->default(0);    
            $table->decimal('telur_kg', 8, 2)->default(0); 
            
            // --- D. Data Pakan ---
            $table->foreignId('pakan_id')->nullable()->constrained('pakans'); 
            $table->decimal('pakan_kg', 8, 2)->default(0); 
            
            // --- E. Metrik Analisa ---
            $table->decimal('fcr', 5, 2)->nullable(); 
            $table->decimal('hdp', 5, 2)->nullable(); 
            
            $table->timestamps();
            
            // Mencegah double input
            $table->unique(['kandang_id', 'tanggal']); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_records');
    }
};