<?php

namespace App\Http\Controllers;

use App\Models\JadwalPerkuliahan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class JadwalPerkuliahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwalPerkuliahan = JadwalPerkuliahan::all();
        return response()->json([
            'message' => 'Daftar JadwalPerkuliahan berhasil diambil',
            'data' => $jadwalPerkuliahan
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
        'kelas_id'              => 'required',
        'dosen_id'              => 'required',
        'ruangan_id'            => 'required',
        'master_sesi_waktu_id'  => 'required',
        'hari'                  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
    ]);

    // 2. Cek jika validasi input dasar gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    
    try {
        $jadwal = JadwalPerkuliahan::create([
            'kelas_id'             => $request->kelas_id,
      $validator = Validator::make($request->all(), [
        'kelas_id'              => 'required',
        'dosen_id'              => 'required',
        'ruangan_id'            => 'required',
        'master_sesi_waktu_id'  => 'required',
        'hari'                  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
    ]);
      'dosen_id'             => $request->dosen_id,
            'ruangan_id'           => $request->ruangan_id,
            'master_sesi_waktu_id' => $request->master_sesi_waktu_id,
            'hari'                 => $request->hari,
        ]);

        return response()->json([
            'message' => 'Jadwal perkuliahan berhasil dibuat',
            'data'    => $jadwal
        ], 201);

    } catch (\Illuminate\Database\QueryException $e) {
       
        return response()->json([
            'message' => 'Gagal membuat jadwal. Terjadi bentrok (Dosen/Ruangan sudah terisi pada waktu tersebut).',
            'error'   => $e->getMessage()
        ], 409);
    }
}


    /**
     * Display the specified resource.
    */
    public function show(JadwalPerkuliahan $jadwalPerkuliahan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalPerkuliahan $jadwalPerkuliahan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalPerkuliahan $jadwalPerkuliahan)
    {
        $jadwal = JadwalPerkuliahan::find($id);

    if (!$jadwal) {
        return response()->json([
            'message' => 'Jadwal perkuliahan tidak ditemukan'
        ], 404);
    }

    // 2. Validasi Input
    $validator = Validator::make($request->all(), [
        'kelas_id'              => 'required',
        'dosen_id'              => 'required',
        'ruangan_id'            => 'required',
        'master_sesi_waktu_id'  => 'required',
        'hari'                  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        // 3. Lakukan Update
        $jadwal->update([
            'kelas_id'             => $request->kelas_id,
            'dosen_id'             => $request->dosen_id,
            'ruangan_id'           => $request->ruangan_id,
            'master_sesi_waktu_id' => $request->master_sesi_waktu_id,
            'hari'                 => $request->hari,
        ]);

        return response()->json([
            'message' => 'Jadwal perkuliahan berhasil diperbarui',
            'data'    => $jadwal
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Tangkap error jika update menyebabkan bentrok data (Unique Constraint di DB)
        return response()->json([
            'message' => 'Gagal memperbarui jadwal. Terjadi bentrok (Dosen/Ruangan/Kelas sudah terisi pada waktu tersebut).',
            'error'   => $e->getMessage()
        ], 409); // 409 Conflict
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalPerkuliahan $jadwalPerkuliahan)
    {
        $jadwal = JadwalPerkuliahan::find($id);

    if (!$jadwal) {
        return response()->json([
            'message' => 'Jadwal perkuliahan tidak ditemukan'
        ], 404);
    }

    // 2. Hapus data
    try {
        $jadwal->delete();

        return response()->json([
            'message' => 'Jadwal perkuliahan berhasil dihapus'
        ], 200);

    } catch (\Exception $e) {
        // Antisipasi jika jadwal sudah terhubung ke tabel lain (misal absensi) dan tidak bisa dihapus
        return response()->json([
            'message' => 'Gagal menghapus jadwal.',
            'error'   => $e->getMessage()
        ], 500);
    }
}   
}
