<?php

namespace App\Http\Controllers;

use App\Models\MasterRuangan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MasterRuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $MasterRuangan = MasterRuangan::all();
        return response()->json([
            'message' => 'Daftar Role berhasil diambil',
            'data' => $MasterRuangan
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
            'kode_ruangan' => 'required|string|max:20|unique:master_ruangan,kode_ruangan',
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required',
            'jenis_ruangan' => 'required|in:Kelas,Laboratorium,Auditorium',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $MasterRuangan = MasterRuangan::create([
            'kode_ruangan' => $request->kode_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
            'jenia_ruangan' => $request->jenis_ruangan,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterRuangan $masterRuangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterRuangan $masterRuangan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterRuangan $masterRuangan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterRuangan $masterRuangan)
    {
        //
    }
}
