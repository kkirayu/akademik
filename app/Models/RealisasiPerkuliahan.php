<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiPerkuliahan extends Model
{
    protected $table = 'realisasi_perkuliahan';

    protected $fillable = [
        'kelas_id',
        'tanggal_aktual',
        'jam_mulai_aktual',
        'jam_selesai_aktual',
        'ruangan_id',
        'dosen_pengajar_id',
        'topik_pembahasan',
        'catatan_kejadian',
        'status_pertemuan',
        'kode_presensi',
        'batas_waktu_presensi'
    ];
}