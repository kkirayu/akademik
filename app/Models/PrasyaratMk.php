<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrasyaratMk extends Model
{
    use HasFactory;

    // --- TAMBAHKAN INI AGAR TIDAK ERROR ---
    protected $table = 'prasyarat_mk';
    // --------------------------------------

    protected $guarded = ['id'];

    // MK yang punya syarat
    public function mataKuliah() {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    // MK yang jadi syaratnya
    public function syarat() {
        return $this->belongsTo(MataKuliah::class, 'mk_syarat_id');
    }
}