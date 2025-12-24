<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPerkuliahan extends Model
{
    use HasFactory;
    protected $table = 'jadwal_perkuliahans';
    protected $guarded = ['id'];

    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id'); }
    public function dosen() { return $this->belongsTo(Dosen::class, 'dosen_id'); }
    public function ruangan() { return $this->belongsTo(MasterRuangan::class, 'ruangan_id'); }
    public function sesi() { return $this->belongsTo(MasterSesiWaktu::class, 'master_sesi_waktu_id'); }
}