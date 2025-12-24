<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSesiWaktu extends Model
{
    use HasFactory;
    protected $table = 'master_sesi_waktus';
    protected $guarded = ['id'];
}