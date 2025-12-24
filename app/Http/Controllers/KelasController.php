<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::all();
        return response()->json([
            'message' => 'Daftar Kelas berhasil diambil',
            'data' => $kelas
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
            'tahun_akademik' => 'required|string',
            'mata_kuliah_id' => 'required|string',
            'nama_kelas' => 'required|string|max:50',
            'kuota' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
    }
}


    public function show(Kelas $kelas)
    {
        //
    }

    public function edit(Kelas $kelas)
    {
        //
    }

    public function update(Request $request, Kelas $kelas)
    {
        $kelas = Kelas::find($id);

    // 2. Cek apakah data ditemukan
    if (!$kelas) {
        return response()->json([
            'message' => 'Data Kelas tidak ditemukan'
        ], 404);
    }

    // 3. Validasi Input
    $validator = Validator::make($request->all(), [
        'tahun_akademik' => 'required|string',
        'mata_kuliah_id' => 'required|string', // Pastikan tipe data sesuai (string/int) dengan database
        'nama_kelas'     => 'required|string|max:50',
        'kuota'          => 'required|string', // Sebaiknya pastikan ini numeric jika di DB integer
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        // 4. Lakukan Update
        $kelas->update([
            'tahun_akademik' => $request->tahun_akademik,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'nama_kelas'     => $request->nama_kelas,
            'kuota'          => $request->kuota,
        ]);

        return response()->json([
            'message' => 'Data Kelas berhasil diperbarui',
            'data'    => $kelas
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'message' => 'Gagal memperbarui data kelas.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        $kelas = Kelas::find($id);

    // 2. Cek ketersediaan
    if (!$kelas) {
        return response()->json([
            'message' => 'Data Kelas tidak ditemukan'
        ], 404);
    }

    try {
        // 3. Hapus data
        $kelas->delete();

        return response()->json([
            'message' => 'Data Kelas berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error ini muncul jika Kelas sudah dipakai di tabel lain (misal: di tabel jadwal atau krs)
        // Code 23000 biasanya terkait Integrity Constraint Violation
        if ($e->getCode() == "23000") {
             return response()->json([
                'message' => 'Data Kelas tidak bisa dihapus karena sudah digunakan dalam Jadwal atau KRS.',
                'error'   => $e->getMessage()
            ], 409); // 409 Conflict
        }

        return response()->json([
            'message' => 'Terjadi kesalahan server saat menghapus data.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}
