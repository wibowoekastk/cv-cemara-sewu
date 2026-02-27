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
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Kolom Role (Admin, Owner, Mandor)
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('mandor')->after('email'); 
            }

            // 2. Kolom Status (Active/Inactive)
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('password');
            }

            // 3. Kolom Avatar (Foto Profil)
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('status');
            }

            // 4. Kolom Unit ID (Relasi ke tabel units)
            // Nullable karena Admin/Owner mungkin tidak punya unit
            if (!Schema::hasColumn('users', 'unit_id')) {
                $table->foreignId('unit_id')
                      ->nullable()
                      ->after('avatar')
                      ->constrained('units')
                      ->onDelete('set null');
            }

            // 5. Kolom Lokasi ID (Untuk menyimpan ID Lokasi Farm)
            if (!Schema::hasColumn('users', 'lokasi_id')) {
                $table->integer('lokasi_id')->nullable()->after('unit_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus Foreign Key dulu sebelum hapus kolom
            if (Schema::hasColumn('users', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }

            // Hapus kolom lainnya
            $columns = ['role', 'status', 'avatar', 'lokasi_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};