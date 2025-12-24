<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'program_studis'; 

    protected $fillable = [
        'fakultas_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];
    
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }
}