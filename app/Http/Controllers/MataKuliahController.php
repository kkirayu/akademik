<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mataKuliah = MataKuliah::all();
        return response()->json([
            'message' => 'Daftar MataKuliah berhasil diambil',
            'data' => $mataKuliah
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
            'prodi_id' => 'required|string',
            'kode_mk' => 'required|string|max:20|unique:table,column,except,id',
            'nama_mk' => 'required|string|max:225',
            'sks' => 'required|string',
            'semester_mk'=> 'required|string',
            'deskripsi' => 'required|string',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $MataKuliah = MataKuliah::create([
            'prodi_id' => $request->prodi_id,
            'kode_mk' => $request->kode_mk,
            'nama_mk' => $request->nama_mk,
            'sks' => $request->sks,
             'semester_mk' => $request->semester_mk,
            'deskripsi' => $request->deskripsi,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(MataKuliah $mataKuliah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MataKuliah $mataKuliah)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $mataKuliah = MataKuliah::find($id);

    if (!$mataKuliah) {
        return response()->json([
            'message' => 'Mata Kuliah tidak ditemukan'
        ], 404);
    }

    // 2. Validasi Input
    $validator = Validator::make($request->all(), [
        'prodi_id'    => 'required|string',
        // PENTING: Ganti 'mata_kuliah' dengan nama tabel Anda di database (biasanya mata_kuliahs atau mata_kuliah)
        // Syntax: unique:nama_tabel,nama_kolom,id_pengecualian
        'kode_mk'     => 'required|string|max:20|unique:mata_kuliah,kode_mk,' . $id, 
        'nama_mk'     => 'required|string|max:225',
        'sks'         => 'required|string', // Sebaiknya numeric/integer, tapi saya ikuti request string Anda
        'semester_mk' => 'required|string',
        'deskripsi'   => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 3. Lakukan Update
    $mataKuliah->update([
        'prodi_id'    => $request->prodi_id,
        'kode_mk'     => $request->kode_mk,
        'nama_mk'     => $request->nama_mk,
        'sks'         => $request->sks,
        'semester_mk' => $request->semester_mk,
        'deskripsi'   => $request->deskripsi,
    ]);

    return response()->json([
        'message' => 'Mata Kuliah berhasil diperbarui',
        'data'    => $mataKuliah
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah = MataKuliah::find($id);

    if (!$mataKuliah) {
        return response()->json([
            'message' => 'Mata Kuliah tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $mataKuliah->delete();

        return response()->json([
            'message' => 'Mata Kuliah berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya mata kuliah ini sudah dipakai di tabel Kelas atau Kurikulum
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Mata Kuliah ini sudah memiliki Kelas aktif atau terdata di KRS mahasiswa.',
                'error'   => $e->getMessage()
            ], 409);
        }

        return response()->json([
            'message' => 'Terjadi kesalahan server.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}