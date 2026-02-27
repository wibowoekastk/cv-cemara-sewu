<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Batch (Induk Angkatan)
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('nama_batch'); // Contoh: "Batch Lebaran 2026"
            $table->date('tanggal_mulai')->nullable();
            $table->boolean('is_active')->default(true); // Status aktif/arsip
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Update Tabel Siklus agar punya relasi ke Batch
        // Cek dulu apakah kolom batch_id sudah ada, kalau belum baru buat
        if (!Schema::hasColumn('siklus', 'batch_id')) {
            Schema::table('siklus', function (Blueprint $table) {
                $table->foreignId('batch_id')
                      ->nullable()
                      ->after('kandang_id')
                      ->constrained('batches')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('siklus', 'batch_id')) {
            Schema::table('siklus', function (Blueprint $table) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            });
        }
        Schema::dropIfExists('batches');
    }
};