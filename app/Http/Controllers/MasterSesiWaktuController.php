<?php

namespace App\Http\Controllers;

use App\Models\MasterSesiWaktu;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MasterSesiWaktuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $MasterSesiWaktu = MasterSesiWaktu::all();
        return response()->json([
            'message' => 'Daftar Role berhasil diambil',
            'data' => $MasterSesiWaktu
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
            'sesi_ke' => 'required|integer',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $MasterSesiWaktu = MasterSesiWaktu::create([
            'sesi_ke' => $request->sesi_ke,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterSesiWaktu $masterSesiWaktu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterSesiWaktu $masterSesiWaktu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterSesiWaktu $masterSesiWaktu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterSesiWaktu $masterSesiWaktu)
    {
        //
    }
}
