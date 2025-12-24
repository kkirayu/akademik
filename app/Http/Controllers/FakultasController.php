<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fakultas = Fakultas::all();
        return response()->json([
            'message' => 'Daftar Fakultas berhasil diambil',
            'data' => $fakultas
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
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Fakultas $fakultas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fakultas $fakultas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fakultas $fakultas, $id)
    {
        // 1. Cari data berdasarkan ID
    $fakultas = Fakultas::find($id);

    // 2. Cek apakah data ditemukan
    if (!$fakultas) {
        return response()->json([
            'message' => 'Data Fakultas tidak ditemukan'
        ], 404);
    }

    // 3. Validasi Input
    $validator = Validator::make($request->all(), [
        // Perhatikan bagian .$id. Ini penting agar validasi mengabaikan ID saat ini
        // Format: unique:table,column,except_id
        'kode_fakultas' => 'required|string|max:10|unique:fakultas,kode_fakultas,' . $id,
        'nama_fakultas' => 'required|string|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    // 4. Lakukan Update
    $fakultas->update([
        'kode_fakultas' => $request->kode_fakultas,
        'nama_fakultas' => $request->nama_fakultas,
    ]);

    return response()->json([
        'message' => 'Fakultas berhasil diperbarui',
        'data' => $fakultas
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fakultas $id)
    {
        $fakultas = Fakultas::find($id);

    // 2. Cek apakah data ditemukan
    if (!$fakultas) {
        return response()->json([
            'message' => 'Data Fakultas tidak ditemukan'
        ], 404);
    }

    // 3. Hapus data
    $fakultas->delete();

    return response()->json([
        'message' => 'Fakultas berhasil dihapus'
    ], 200);
    }
}
