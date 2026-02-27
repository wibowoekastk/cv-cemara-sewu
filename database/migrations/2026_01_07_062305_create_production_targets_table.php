<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lokasi_id');
            $table->unsignedBigInteger('unit_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('hd', 5, 2);
            $table->decimal('egg_weight', 5, 2);
            $table->decimal('fcr', 4, 2);
            $table->decimal('bw', 8, 2);
            $table->decimal('mortality', 5, 2);
            $table->enum('status', ['active', 'history'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_targets');
    }
};