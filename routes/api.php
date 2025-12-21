<?php

use App\Http\Controllers\FakultasController;
use App\Http\Controllers\JadwalPerkuliahanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DosenController;

use App\Models\JadwalPerkuliahan;

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



Route::get('jadwalPerkuliahan',[JadwalPerkuliahanController::class, 'index']);

