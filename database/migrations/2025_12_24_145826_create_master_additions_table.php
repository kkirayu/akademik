<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Gedung
        Schema::create('gedung', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gedung', 10)->unique();
            $table->string('nama_gedung', 100);
            $table->text('lokasi')->nullable(); // Kampus A, Kampus B, dll
            $table->timestamps();
        });

        // 2. Modifikasi master_ruangan (Menambahkan relasi ke gedung)
        Schema::table('master_ruangans', function (Blueprint $table) {
            $table->foreignId('gedung_id')->nullable()->constrained('gedung')->onDelete('set null');
        });

        // 3. Tabel Kurikulum
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('program_studis')->onDelete('cascade');
            $table->string('nama_kurikulum', 100); // Contoh: Kurikulum Merdeka 2024
            $table->year('tahun_mulai');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->foreignId('kurikulum_id')->nullable()->after('id')->constrained('kurikulum')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropForeign(['kurikulum_id']);
            $table->dropColumn('kurikulum_id');
        });
        
        Schema::dropIfExists('kurikulum');

        Schema::table('master_ruangans', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropColumn('gedung_id');
        });

        Schema::dropIfExists('gedung');
    }
};