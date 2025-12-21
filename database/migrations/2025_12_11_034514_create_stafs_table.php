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
        Schema::create('stafs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nama_depan', 100);
            $table->string('nama_belakang', 100)->nullable();
            $table->string('nomor_induk_pegawai', 30)->unique();
            $table->string('unit_kerja', 100)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->text('alamat_staf')->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->enum('status_keaktifan', ['Aktif', 'Non-Aktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stafs');
    }
};
