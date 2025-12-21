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
        Schema::create('krs_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');

            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->onDelete('restrict');
            $table->enum('status_approval', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->string('nilai_akhir', 5)->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'kelas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs_mahasiswas');
    }
};
