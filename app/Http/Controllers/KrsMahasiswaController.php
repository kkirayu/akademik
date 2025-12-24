<?php

namespace App\Http\Controllers;

use App\Models\KrsMahasiswa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KrsMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $krsMahasiswa = KrsMahasiswa::all();
        return response()->json([
            'message' => 'Daftar KrsMahasiswa berhasil diambil',
            'data' => $krsMahasiswa
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
        $validator = Validator::make($request->all(), [
        'mahasiswa_id'      => 'required',
        'kelas_id'          => 'required',
        'tahun_akademik_id' => 'required',
        'status_approval'   => 'nullable|in:Menunggu,Disetujui,Ditolak',
        'nilai_akhir'       => 'nullable|string|max:5',
    ]);

    // 2. Cek jika validasi input dasar gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 3. Simpan data ke tabel krs_mahasiswas
    try {
        $krs = KrsMahasiswa::create([
            'mahasiswa_id'      => $request->mahasiswa_id,
            'kelas_id'          => $request->kelas_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'status_approval'   => $request->status_approval ?? 'Menunggu',
            'nilai_akhir'       => $request->nilai_akhir,
        ]);

        return response()->json([
            'message' => 'KRS Mahasiswa berhasil dibuat',
            'data'    => $krs
        ], 201);

    } catch (\Illuminate\Database\QueryException $e) {

        return response()->json([
            'message' => 'Gagal menyimpan KRS. Mahasiswa sudah mengambil kelas ini.',
            'error'   => $e->getMessage()
        ], 409);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(KrsMahasiswa $krsMahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KrsMahasiswa $krsMahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KrsMahasiswa $krsMahasiswa)
    {
        // 1. Cari data KRS
    $krs = KrsMahasiswa::find($id);

    if (!$krs) {
        return response()->json([
            'message' => 'Data KRS tidak ditemukan'
        ], 404);
    }

    // 2. Validasi Input
    $validator = Validator::make($request->all(), [
        'mahasiswa_id'      => 'required',
        'kelas_id'          => 'required',
        'tahun_akademik_id' => 'required',
        'status_approval'   => 'nullable|in:Menunggu,Disetujui,Ditolak',
        'nilai_akhir'       => 'nullable|string|max:5',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        // 3. Lakukan Update
        $krs->update([
            'mahasiswa_id'      => $request->mahasiswa_id,
            'kelas_id'          => $request->kelas_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'status_approval'   => $request->status_approval ?? $krs->status_approval, // Gunakan data lama jika null
            'nilai_akhir'       => $request->nilai_akhir,
        ]);

        return response()->json([
            'message' => 'Data KRS berhasil diperbarui',
            'data'    => $krs
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error ini muncul jika kombinasi (mahasiswa + kelas + tahun) menjadi duplikat setelah diedit
        return response()->json([
            'message' => 'Gagal memperbarui KRS. Mahasiswa sudah terdaftar di kelas tersebut.',
            'error'   => $e->getMessage()
        ], 409);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KrsMahasiswa $krsMahasiswa)
    {
        $krs = KrsMahasiswa::find($id);

    if (!$krs) {
        return response()->json([
            'message' => 'Data KRS tidak ditemukan'
        ], 404);
    }

    // 2. Hapus data
    try {
        $krs->delete();

        return response()->json([
            'message' => 'Data KRS berhasil dihapus (Mata kuliah dibatalkan)'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal menghapus data KRS.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}