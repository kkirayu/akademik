<?php

use App\Http\Controllers\FakultasController;
use App\Http\Controllers\JadwalPerkuliahanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\KrsMahasiswa;
use App\Http\Controllers\MasterRuanganController;
use App\Http\Controllers\MasterSesiWaktuController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\KelasController;


use App\Models\JadwalPerkuliahan;

Route::get('/test', function () {
    return 'API Masuk!';
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Role
Route::get('roles', [RoleController::class, 'index']); // READ ALL
Route::post('roles', [RoleController::class, 'store']);

//Fakultas
Route::get('fakultas', [FakultasController::class, 'index']);
Route::post('fakultas', [FakultasController::class, 'store']);

//User
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);

//Dosen
Route::get('dosens', [DosenController::class, 'index']);
Route::post('dosens', [DosenController::class, 'store']);

//Mahasiswa
Route::get('mahasiswa', [MahasiswaController::class, 'index']);
Route::post('mahasiswa', [MahasiswaController::class, 'store']);

//Krs Mahasiswa
Route::get('krs-mahasiswa', [KrsMahasiswaController::class, 'index']);
Route::post('krs-mahasiswa', [KrsMahasiswaController::class, 'store']);

//Master Ruangan
Route::get('master-ruangan', [MasterRuanganController::class, 'index']);
Route::post('master-ruangan', [MasterRuanganController::class, 'store']);

//Master Sesi Waktu
Route::get('master-sesi-waktu', [MasterSesiWaktuController::class, 'index']);
Route::post('master-sesi-waktu', [MasterSesiWaktuController::class, 'store']);

//Mata Kuliah
Route::get('mata-kuliah', [MataKuliahController::class, 'index']);
Route::post('mata-kuliah', [MataKuliahController::class, 'store']);

//Program Studi
Route::get('program-studi', [ProgramStudiController::class, 'index']);
Route::post('program-studi', [ProgramStudiController::class, 'store']);

// Jadwal Perkuliahan
Route::get('jadwal-perkuliahan', [JadwalPerkuliahanController::class, 'index']);
Route::post('jadwal-perkuliahan', [JadwalPerkuliahanController::class, 'store']);


// Kelas
Route::get('kelas', [KelasController::class, 'index']);
Route::post('kelas', [KelasController::class, 'store']);

