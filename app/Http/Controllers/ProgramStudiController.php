<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    public function index()
    {
        // Load data prodi beserta data fakultasnya
        $prodi = ProgramStudi::with('fakultas')->get();
        return response()->json(['success' => true, 'data' => $prodi]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode_prodi'  => 'required|unique:program_studis,kode_prodi|max:10',
            'nama_prodi'  => 'required|string|max:100',
            'jenjang'     => 'required|in:D3,S1,S2,S3', // Validasi Enum
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $prodi = ProgramStudi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program Studi Berhasil Ditambahkan',
            'data'    => $prodi
        ], 201);
    }

    public function show($id)
    {
        $prodi = ProgramStudi::with('fakultas')->find($id);

        if ($prodi) {
            return response()->json(['success' => true, 'data' => $prodi]);
        }
        return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);
    }

    public function update(Request $request, $id)
    {
        $prodi = ProgramStudi::find($id);
        if (!$prodi) return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode_prodi'  => 'required|max:10|unique:program_studis,kode_prodi,' . $id,
            'nama_prodi'  => 'required|string|max:100',
            'jenjang'     => 'required|in:D3,S1,S2,S3',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $prodi->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program Studi Berhasil Diupdate',
            'data'    => $prodi
        ]);
    }

    public function destroy($id)
    {
        $prodi = ProgramStudi::find($id);
        if (!$prodi) return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);

        // Jika ada tabel lain yg berelasi ke prodi (misal mahasiswa), 
        // pastikan dicek juga jika database pakai restrict.
        // Tapi untuk saat ini kita langsung delete.
        try {
            $prodi->delete();
            return response()->json(['success' => true, 'message' => 'Program Studi Berhasil Dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Hapus: Data sedang digunakan di tabel lain.'], 400);
        }
    }
}
