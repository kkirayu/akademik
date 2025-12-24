<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TahunAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $TahunAkademik = TahunAkademik::all();
        return response()->json([
            'message' => 'Daftar TahunAkademik berhasil diambil',
            'data' => $TahunAkademik
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
            'kode_tahun' => 'required|string|max:10|unique:TahunAkademik,kode_tahun',
            'nama_tahun' => 'required|string|max:50',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'status' => 'required|in:Aktif,Selesai,Direncanakan',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $TahunAkademik = TahunAkademik::create([
            'kode_tahun' => $request->kode_tahun,
            'nama_tahun' => $request->nama_tahun,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,

        ]);

        return response()->json([
            'message' => 'TahunAkademik berhasil dibuat',
            'data' => $TahunAkademik
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAkademik $tahunAkademik)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAkademik $tahunAkademik)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
         $validator = Validator::make($request->all(), [
            'kode_tahun' => 'required|string|max:10|unique:TahunAkademik,kode_tahun',
            'nama_tahun' => 'required|string|max:50',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'status' => 'required|in:Aktif,Selesai,Direncanakan',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $TahunAkademik = TahunAkademik::create([
            'kode_tahun' => $request->kode_tahun,
            'nama_tahun' => $request->nama_tahun,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,

        ]);

        return response()->json([
            'message' => 'TahunAkademik berhasil dibuat',
            'data' => $TahunAkademik
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAkademik $tahunAkademik)
    {
        // 1. Cari data
    $tahunAkademik = TahunAkademik::find($id);

    if (!$tahunAkademik) {
        return response()->json([
            'message' => 'Data Tahun Akademik tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $tahunAkademik->delete();

        return response()->json([
            'message' => 'Tahun Akademik berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya Tahun ini sudah dipakai di tabel KRS, Jadwal, atau Pembayaran SPP
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Tahun Akademik ini sudah memiliki data terkait (KRS/Jadwal/Pembayaran).',
                'error'   => $e->getMessage()
            ], 409); // 409 Conflict
        }

        return response()->json([
            'message' => 'Terjadi kesalahan server.',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}
