<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- 1. Import ini

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
        'last_login' 
    ];
    
    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function mahasiswa() {
        return $this->hasOne(Mahasiswa::class);
    }

    public function dosen() {
        return $this->hasOne(Dosen::class);
    }
}