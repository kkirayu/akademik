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
        $sesiWaktu = MasterSesiWaktu::find($id);

    if (!$sesiWaktu) {
        return response()->json([
            'message' => 'Data Sesi Waktu tidak ditemukan'
        ], 404);
    }

    // 2. Validasi Input
    $validator = Validator::make($request->all(), [
        // Validasi unique:table,column,except_id
        // Pastikan nama tabel di database benar (misal: master_sesi_waktu)
        'sesi_ke'     => 'required|integer|unique:master_sesi_waktu,sesi_ke,' . $id,
        'jam_mulai'   => 'required', // Opsional: tambahkan format |date_format:H:i
        'jam_selesai' => 'required', // Opsional: tambahkan format |date_format:H:i
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 3. Lakukan Update
    $sesiWaktu->update([
        'sesi_ke'     => $request->sesi_ke,
        'jam_mulai'   => $request->jam_mulai,
        'jam_selesai' => $request->jam_selesai,
    ]);

    return response()->json([
        'message' => 'Data Sesi Waktu berhasil diperbarui',
        'data'    => $sesiWaktu
    ], 200);
    }


    public function destroy(MasterSesiWaktu $masterSesiWaktu)
    {
        $sesiWaktu = MasterSesiWaktu::find($id);

    if (!$sesiWaktu) {
        return response()->json([
            'message' => 'Data Sesi Waktu tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $sesiWaktu->delete();

        return response()->json([
            'message' => 'Data Sesi Waktu berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error 23000: Integrity Constraint Violation
        // Artinya Sesi ini sedang dipakai di tabel Jadwal
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Sesi waktu ini sedang digunakan dalam Jadwal Perkuliahan.',
                'error'   => $e->getMessage()
            ], 409);
        }

        return response()->json([
            'message' => 'Terjadi kesalahan server.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}