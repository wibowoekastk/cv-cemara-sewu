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
        // 1. TABEL MASTER OBAT (Katalog Obat)
        // Hanya menyimpan nama dan spesifikasi, bukan jumlah stok.
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat');
            $table->string('jenis_obat')->nullable(); // Contoh: Vaksin, Antibiotik, Vitamin
            $table->string('satuan'); // Contoh: Botol, Sachet, Kg, Liter
            $table->integer('min_stok')->default(5); // Alert jika total stok di bawah ini
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. TABEL BATCH STOK (Inventory Logistik - Inti FEFO)
        // Setiap pembelian obat baru akan masuk sini sebagai baris baru (Batch Baru).
        Schema::create('obat_batches', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel obats (Master)
            $table->foreignId('obat_id')->constrained('obats')->onDelete('cascade');
            
            $table->string('kode_batch')->nullable(); // Kode produksi dari pabrik (opsional)
            $table->date('tgl_masuk');
            $table->date('tgl_kadaluarsa'); // KUNCI UTAMA FEFO (Expired Date)
            
            $table->integer('stok_awal'); // Jumlah saat dibeli
            $table->integer('stok_saat_ini'); // Jumlah sisa (akan berkurang saat dipakai)
            
            $table->decimal('harga_beli', 12, 2)->nullable(); // Untuk laporan HPP (opsional)
            
            // Status batch: 
            // 'active' (bisa dipakai), 'expired' (kadaluarsa), 'empty' (habis)
            $table->enum('status', ['active', 'expired', 'empty'])->default('active');
            
            $table->timestamps();
        });

        // 3. TABEL RIWAYAT PEMAKAIAN (Transaksi Keluar)
        // Mencatat siapa yang ambil, untuk apa, dan batch mana yang terpotong.
        Schema::create('obat_usages', function (Blueprint $table) {
            $table->id();
            // Relasi ke user (siapa yang input)
            $table->foreignId('user_id')->constrained('users'); 
            // Relasi ke batch spesifik (agar tahu batch mana yang dipakai)
            $table->foreignId('obat_batch_id')->constrained('obat_batches'); 
            
            $table->integer('jumlah_pakai');
            $table->date('tgl_pakai');
            
            $table->string('kategori_pemakaian')->nullable(); // Contoh: Pengobatan Rutin, Wabah
            $table->text('keterangan')->nullable(); // Detail kandang mana atau penyakit apa
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel dengan urutan terbalik (anak dulu baru induk)
        Schema::dropIfExists('obat_usages');
        Schema::dropIfExists('obat_batches');
        Schema::dropIfExists('obats');
    }
};