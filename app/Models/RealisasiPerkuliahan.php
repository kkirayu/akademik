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

    public function ruangan()
    {
        // Pastikan nama Model kamu 'MasterRuangan' atau 'Ruangan'
        // Sesuaikan dengan nama file di app/Models/MasterRuangan.php
        return $this->belongsTo(MasterRuangan::class, 'ruangan_id');
    }

    /**
     * Relasi ke Tabel Kelas
     * Foreign Key: kelas_id
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Tabel Dosen (Dosen Pengajar saat itu)
     * Foreign Key: dosen_pengajar_id
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pengajar_id');
    }
}