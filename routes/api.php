<?php

use App\Http\Controllers\FakultasController;
use App\Http\Controllers\JadwalPerkuliahanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\KrsMahasiswaController;
use App\Http\Controllers\MasterRuanganController;
use App\Http\Controllers\MasterSesiWaktuController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\GedungController;

use App\Models\JadwalPerkuliahan;

Route::get('/test', function () {
    return 'API Masuk!';
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Akun
Route::apiResource('mahasiswa', MahasiswaController::class);
Route::apiResource('dosen', DosenController::class);

//Role
Route::apiResource('role', RoleController::class);

// Prodi & Fakultas
Route::apiResource('fakultas', FakultasController::class);
Route::apiResource('prodi', ProgramStudiController::class);


//Akademik
Route::apiResource('matakuliah', MataKuliahController::class);
Route::apiResource('tahun-akademik', TahunAkademikController::class);
Route::apiResource('ruangan', MasterRuanganController::class);

//Jadwal
Route::apiResource('sesi-waktu', MasterSesiWaktuController::class);
Route::apiResource('kelas', KelasController::class);
Route::apiResource('jadwal', JadwalPerkuliahanController::class);
Route::apiResource('krs', \App\Http\Controllers\KrsMahasiswaController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    
    Route::apiResource('jurnal-kuliah', \App\Http\Controllers\RealisasiPerkuliahanController::class);

    Route::post('/penilaian-kolektif', [\App\Http\Controllers\Api\PenilaianController::class, 'store']);
    Route::get('/penilaian-kolektif/{kelas_id}', [\App\Http\Controllers\Api\PenilaianController::class, 'show']);

    Route::get('/khs', [\App\Http\Controllers\Api\HasilStudiController::class, 'lihatKhs']);
    Route::get('/transkrip', [\App\Http\Controllers\Api\HasilStudiController::class, 'lihatTranskrip']);

});

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::apiResource('gedung', GedungController::class);
});

// Mahasiswa dan Dosen boleh akses
Route::middleware(['auth:sanctum', 'role:Mahasiswa,Dosen'])->group(function () {
    // ...
});