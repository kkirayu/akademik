<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Prasyarat Mata Kuliah (Self Reference)
        Schema::create('prasyarat_mk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('mk_syarat_id')->constrained('mata_kuliahs')->onDelete('cascade');
            // Syaratnya apa? Harus sudah lulus, atau minimal nilai D, atau sedang diambil bersamaan (co-requisite)
            $table->enum('jenis_syarat', ['Wajib Lulus', 'Minimal Nilai D', 'Sedang Ambil'])->default('Wajib Lulus');
            
            $table->unique(['mk_id', 'mk_syarat_id']); // Mencegah duplikasi syarat
            $table->timestamps();
        });

        // 2. Team Teaching (Kelas Dosen)
        Schema::create('kelas_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade'); 
            $table->boolean('is_koordinator')->default(false); // Penanggung jawab kelas
            $table->decimal('sks_beban', 4, 2)->default(0); // Hitungan beban kerja dosen tsb
            
            $table->timestamps();
        });

        // 3. User Unit Access (Data Scoping untuk Admin)
        Schema::create('user_unit_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->onDelete('cascade');
            $table->foreignId('prodi_id')->nullable()->constrained('program_studis')->onDelete('cascade');
            
            $table->string('role_scope', 50)->comment('Contoh: Admin Prodi, Dekan, Kaprodi');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_unit_access');
        Schema::dropIfExists('kelas_dosen');
        Schema::dropIfExists('prasyarat_mk');
    }
};