<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dosen extends Model
{
    use HasFactory;
    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'nama_depan',
        'nama_belakang',
        'gelar_depan',
        'gelar_belakang',
        'nip_dosen',
        'email_institusi',
        'alamat_dosen',
        'nomor_telepon',
        'status_keaktifan',
    ];

    // Relasi ke User (Setiap Dosen memiliki 1 Akun User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
