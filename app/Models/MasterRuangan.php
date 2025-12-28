<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRuangan extends Model
{
    use HasFactory;
    protected $table = 'master_ruangans';
    protected $guarded = ['id'];


    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }
}