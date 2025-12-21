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
        Schema::create('tahun_akademiks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tahun', 10)->unique();
            $table->string('nama_tahun', 50);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status',['Aktif','Selesai','Direncanakan'])->default('Direncanakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_akademiks');
    }
};
