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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        //
    }
}
