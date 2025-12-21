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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataKuliah $mataKuliah)
    {
        //
    }
}
