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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap', 255);
            $table->string('nim', 20)->unique();
            $table->integer('angkatan');
            $table->foreignId('prodi_id')->constrained('program_studis')->onDelete('restrict');

            $table->foreignId('dosen_wali_id')->nullable()->constrained('dosens')->onDelete('set null');
            $table->text('alamat')->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->enum('status_mahasiswa', ['Aktif', 'Cuti', 'Lulus', 'Mengundurkan Diri', 'DO'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
