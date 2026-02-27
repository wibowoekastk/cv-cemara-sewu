<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Pakan (Stok Pusat ada di sini)
        Schema::create('pakans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pakan');     
            $table->string('jenis_pakan');    
            $table->string('satuan')->default('kg');
            
            // Stok Pusat (Gudang Utama Admin)
            $table->double('stok_pusat')->default(0); 
            $table->integer('min_stok')->default(100);
            
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Stok Pakan di Unit (Gudang Mandor) - BARU
        Schema::create('unit_pakan_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('pakan_id')->constrained('pakans')->onDelete('cascade');
            $table->double('jumlah_stok')->default(0);
            $table->timestamps();
        });

        // 3. Tabel Mutasi Pakan (Mencatat Masuk, Kirim, Terima, Pakai) - REVISI TOTAL
        Schema::create('pakan_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pakan_id')->constrained('pakans')->onDelete('cascade');
            $table->foreignId('user_id')->nullable(); // Siapa yang input

            // Asal & Tujuan (Nullable karena bisa dari Supplier atau ke Kandang)
            $table->foreignId('dari_unit_id')->nullable()->constrained('units')->onDelete('set null'); // Null = Pusat/Supplier
            $table->foreignId('ke_unit_id')->nullable()->constrained('units')->onDelete('set null');   // Null = Pusat/Musnah
            $table->foreignId('kandang_id')->nullable()->constrained('kandangs')->onDelete('set null'); // Diisi jika pemakaian

            $table->date('tanggal');
            $table->string('jenis_mutasi'); // 'masuk_pusat', 'distribusi_ke_unit', 'terima_unit', 'pemakaian_kandang'
            $table->double('jumlah');
            
            // Status Pengiriman
            $table->string('status')->default('selesai'); // 'selesai', 'pending_terima'
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakan_mutations');
        Schema::dropIfExists('unit_pakan_stocks');
        Schema::dropIfExists('pakans');
    }
};