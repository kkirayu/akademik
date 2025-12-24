<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ProgramStudi = ProgramStudi::all();
        return response()->json([
            'message' => 'Daftar Program studi berhasil diambil',
            'data' => $ProgramStudi
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
            'fakultas_id' => 'required|integer',
           'kode_prodi' => 'required|string|max:10|unique:program_studi,kode_prodi',
            'nama_prodi' => 'required',
            'jenjang' => 'required|in:D3,S1,S2,S3',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $ProgramStudi = ProgramStudi::create([
            'fakulas_id' => $request->fakultas_id,
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'jenjang' => $request->jenjang,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramStudi $programStudi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramStudi $programStudi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramStudi $programStudi)
    {
        $programStudi = ProgramStudi::find($id);

    // 2. Cek apakah data ditemukan
    if (!$programStudi) {
        return response()->json([
            'message' => 'Program Studi tidak ditemukan'
        ], 404);
    }

    // 3. Validasi Input
    $validator = Validator::make($request->all(), [
        'fakultas_id' => 'required|integer',
        // Format: unique:nama_tabel,nama_kolom,id_pengecualian
        'kode_prodi'  => 'required|string|max:10|unique:program_studi,kode_prodi,' . $id,
        'nama_prodi'  => 'required|string|max:100',
        'jenjang'     => 'required|in:D3,S1,S2,S3',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 4. Lakukan Update
    $programStudi->update([
        'fakultas_id' => $request->fakultas_id,
        'kode_prodi'  => $request->kode_prodi,
        'nama_prodi'  => $request->nama_prodi,
        'jenjang'     => $request->jenjang,
    ]);

    return response()->json([
        'message' => 'Program Studi berhasil diperbarui',
        'data'    => $programStudi
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudi $programStudi)
    {
        $programStudi = ProgramStudi::find($id);

    if (!$programStudi) {
        return response()->json([
            'message' => 'Program Studi tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $programStudi->delete();

        return response()->json([
            'message' => 'Program Studi berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya Prodi ini masih punya Mahasiswa, Dosen, atau Mata Kuliah
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Program Studi ini masih memiliki data Mahasiswa atau Mata Kuliah aktif.',
                'error'   => $e->getMessage()
            ], 409); // 409 Conflict
        }

        return response()->json([
            'message' => 'Terjadi kesalahan server.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}
