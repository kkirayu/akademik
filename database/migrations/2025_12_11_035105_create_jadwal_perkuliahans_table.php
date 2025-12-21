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
        Schema::create('jadwal_perkuliahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('master_ruangans')->onDelete('restrict');
            $table->foreignId('master_sesi_waktu_id')->constrained('master_sesi_waktus')->onDelete('restrict');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']);
            $table->timestamps();

            $table->unique(['dosen_id', 'master_sesi_waktu_id', 'hari', 'kelas_id'], 'unique_dosen_jadwal');
            $table->unique(['ruangan_id', 'master_sesi_waktu_id', 'hari'], 'unique_ruangan_jadwal');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_perkuliahans');
    }
};
