<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah Kolom Kode Presensi di Jurnal Dosen (Realisasi)
        Schema::table('realisasi_perkuliahan', function (Blueprint $table) {
            $table->string('kode_presensi', 6)->nullable()->after('status_pertemuan'); // Kode 6 digit
            $table->timestamp('batas_waktu_presensi')->nullable()->after('kode_presensi'); // Kode expired kapan
        });

        // 2. Tabel Presensi Mahasiswa (Log Absen)
        Schema::create('presensi_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realisasi_id')->constrained('realisasi_perkuliahan')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->dateTime('waktu_presensi');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Hadir');
            $table->string('device_info')->nullable(); // Opsional: catat HP apa
            $table->timestamps();
        });

        // 3. Tabel Materi Kuliah (Dosen Upload)
        Schema::create('materi_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('judul_materi');
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable(); // Lokasi file PDF disimpan
            $table->timestamps();
        });

        // 4. Tabel Tugas (Dosen Buat Slot Tugas)
        Schema::create('tugas_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('judul_tugas');
            $table->text('deskripsi')->nullable();
            $table->string('file_soal_path')->nullable(); // PDF Soal (Opsional)
            $table->dateTime('deadline');
            $table->timestamps();
        });

        // 5. Tabel Pengumpulan Tugas (Mahasiswa Upload)
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas_kuliah')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->string('file_jawaban_path'); // PDF Jawaban
            $table->dateTime('waktu_pengumpulan');
            $table->decimal('nilai', 5, 2)->nullable(); // Nilai dari Dosen
            $table->text('catatan_dosen')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('tugas_kuliah');
        Schema::dropIfExists('materi_kuliah');
        Schema::dropIfExists('presensi_mahasiswa');
        Schema::table('realisasi_perkuliahan', function (Blueprint $table) {
            $table->dropColumn(['kode_presensi', 'batas_waktu_presensi']);
        });
    }
};