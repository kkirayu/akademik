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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudi $programStudi)
    {
        //
    }
}
