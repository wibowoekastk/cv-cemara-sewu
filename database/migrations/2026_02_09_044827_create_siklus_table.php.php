<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hanya Buat Tabel Siklus
        Schema::create('siklus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandangs')->onDelete('cascade');
            
            // Info Batch Dasar
            $table->date('tanggal_chick_in');
            $table->string('jenis_ayam')->default('Layer'); // Layer / DOC
            $table->integer('populasi_awal'); // Jumlah ayam saat masuk
            $table->integer('umur_awal_minggu')->default(18); // Umur ayam (minggu) saat masuk kandang
            $table->decimal('harga_satuan', 12, 2)->nullable(); // Harga beli per ekor (opsional)
            $table->string('vendor_bibit')->nullable(); // Asal bibit
            
            // Status Siklus
            $table->enum('status', ['Aktif', 'Afkir', 'Selesai'])->default('Aktif');
            $table->date('tanggal_selesai')->nullable(); // Tanggal afkir/kosong kandang
            $table->integer('total_afkir')->default(0); // Jumlah ayam yang diafkir di akhir
            
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // HAPUS BAGIAN Schema::table('daily_records'...) DARI SINI
        // Karena tabel daily_records belum ada saat file ini jalan.
        // Kolom siklus_id sudah kita masukkan langsung di file migration daily_records yang baru.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siklus');
    }
};