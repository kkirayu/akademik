<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas'; 
    protected $guarded = ['id'];

    public function mataKuliah() {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }
    public function tahunAkademik() {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }
}