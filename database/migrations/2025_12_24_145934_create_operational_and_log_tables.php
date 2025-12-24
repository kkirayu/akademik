<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('realisasi_perkuliahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            
            $table->date('tanggal_aktual');
            $table->time('jam_mulai_aktual');
            $table->time('jam_selesai_aktual');
            
            $table->foreignId('ruangan_id')->constrained('master_ruangans');
            $table->foreignId('dosen_pengajar_id')->constrained('dosens');
            
            $table->text('topik_pembahasan')->nullable();
            $table->text('catatan_kejadian')->nullable(); 
            
            $table->enum('status_pertemuan', ['Terlaksana', 'Dibatalkan', 'Pengganti', 'Kosong'])->default('Terlaksana');
            
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('action'); 
            $table->string('table_name')->nullable();
            $table->unsignedBigInteger('record_id')->nullable(); 
            
            $table->json('old_values')->nullable(); 
            $table->json('new_values')->nullable(); 
            
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('realisasi_perkuliahan');
    }
};