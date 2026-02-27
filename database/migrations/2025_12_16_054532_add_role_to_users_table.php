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
            // Menambahkan kolom role setelah password
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['owner', 'admin', 'mandor'])->default('mandor')->after('password');
            }
            
            // Menambahkan kolom status
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
            
            // Menambahkan unit_id dan lokasi_id (nullable)
            if (!Schema::hasColumn('users', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'lokasi_id')) {
                $table->unsignedBigInteger('lokasi_id')->nullable()->after('unit_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'unit_id', 'lokasi_id']);
        });
    }
};