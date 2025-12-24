<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
   public function index()
    {
        $fakultas = Fakultas::all();
        return response()->json(['success' => true, 'data' => $fakultas]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_fakultas' => 'required|unique:fakultas,kode_fakultas|max:10',
            'nama_fakultas' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $fakultas = Fakultas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fakultas Berhasil Ditambahkan',
            'data'    => $fakultas
        ], 201);
    }

    public function show($id)
    {
        $fakultas = Fakultas::find($id);

        if ($fakultas) {
            return response()->json(['success' => true, 'data' => $fakultas]);
        }
        return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);
    }

    public function update(Request $request, $id)
    {
        $fakultas = Fakultas::find($id);
        if (!$fakultas) return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'kode_fakultas' => 'required|max:10|unique:fakultas,kode_fakultas,' . $id,
            'nama_fakultas' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $fakultas->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fakultas Berhasil Diupdate',
            'data'    => $fakultas
        ]);
    }

    public function destroy($id)
    {
        $fakultas = Fakultas::find($id);
        if (!$fakultas) return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);

        // Cek apakah fakultas ini punya prodi? (Karena database restrict)
        if ($fakultas->prodi()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Hapus: Masih ada Prodi di dalam fakultas ini.'
            ], 400);
        }

        $fakultas->delete();

        return response()->json(['success' => true, 'message' => 'Fakultas Berhasil Dihapus']);
    }
}
