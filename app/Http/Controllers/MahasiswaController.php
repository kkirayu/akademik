<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mahasiswa = Mahasiswa::all();
        return response()->json([
            'message' => 'Daftar Role berhasil diambil',
            'data' => $mahasiswa
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input sesuai schema mahasiswa
    $validator = Validator::make($request->all(), [
        'user_id'          => 'required|unique:mahasiswas,user_id',
        'nama_lengkap'     => 'required|string|max:255',
        'nim'              => 'required|string|max:20|unique:mahasiswas,nim',
        'angkatan'         => 'required|integer',
        'prodi_id'         => 'required',
        'dosen_wali_id'    => 'nullable',
        'alamat'           => 'nullable|string',
        'nomor_telepon'    => 'nullable|string|max:20',
        'status_mahasiswa' => 'required|in:Aktif,Cuti,Lulus,Mengundurkan Diri,DO',
    ]);

    // 2. Cek jika validasi gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 3. Simpan data ke tabel mahasiswas
    $mahasiswa = Mahasiswa::create([
        'user_id'          => $request->user_id,
        'nama_lengkap'     => $request->nama_lengkap,
        'nim'              => $request->nim,
        'angkatan'         => $request->angkatan,
        'prodi_id'         => $request->prodi_id,
        'dosen_wali_id'    => $request->dosen_wali_id,
        'alamat'           => $request->alamat,
        'nomor_telepon'    => $request->nomor_telepon,
        'status_mahasiswa' => $request->status_mahasiswa,
    ]);

    // 4. Return response sukses
    return response()->json([
        'message' => 'Mahasiswa berhasil dibuat',
        'data'    => $mahasiswa
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        //
    }
}
