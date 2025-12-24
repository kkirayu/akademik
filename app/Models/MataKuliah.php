<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;
    protected $table = 'mata_kuliahs';
    protected $guarded = ['id'];

    public function prodi() {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }
}