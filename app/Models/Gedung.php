<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    use HasFactory;

    protected $table = 'gedung'; 

    protected $guarded = ['id'];

    public function ruangan() {
        return $this->hasMany(MasterRuangan::class, 'gedung_id');
    }
}