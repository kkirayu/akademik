<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nim',
        'angkatan',
        'prodi_id',
        'dosen_wali_id',
        'alamat',
        'nomor_telepon',
        'status_mahasiswa'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Prodi
    public function prodi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }
}