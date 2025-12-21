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
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nama_depan', 100);
            $table->string('nama_belakang', 100)->nullable();
            $table->string('gelar_depan', 50)->nullable();
            $table->string('gelar_belakang', 50)->nullable();
            $table->string('nip_dosen', 30)->unique();
            $table->string('email_institusi', 100)->unique()->nullable();
            $table->text('alamat_dosen')->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->enum('status_keaktifan', ['Aktif', 'Cuti', 'Pensiun'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
