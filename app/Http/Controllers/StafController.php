<?php

namespace App\Http\Controllers;

use App\Models\Staf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class StafController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Staf = Staf::all();
        return response()->json([
            'message' => 'Daftar staf berhasil diambil',
            'data' => $Staf
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
            'id' => 'required',
            'user_id' => 'required',
            'nama_depan' => 'required|string|max:100',
            'nama_belakang' => 'string|max:100',
            'nomor_induk_pegawai' => 'string|max:30|unique',
            'unit_kerja' => 'string|max:100',
            'jabatan' => 'string|max:100',
            'alamat_staf' => 'string',
            'nomor_telepon' => 'string|max:20',
            'status_keaktifan' => 'required|in:Aktif,Non-Aktif',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $Staf = Staf::create([
            'id' => $request->id,
            'user_id' => $request->user_id,
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'nomor_induk_pegawai' => $request->nomor_induk_pegawai,
            'unit_kerja' => $request->unit_kerja,
            'jabatan' => $request->jabatan,
            'alamat_staf' => $request->alamat_staf,
            'nomor_telepon' => $request->nomor_telepon,
            'status_kealtifan' => $request->status_keaktifan,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Staf $staf)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staf $staf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staf $staf)
    {
        $staf = Staf::find($id);

    if (!$staf) {
        return response()->json([
            'message' => 'Data Staf tidak ditemukan'
        ], 404);
    }

    // 2. Validasi Input
    // Catatan: Saya menambahkan 'nullable' pada field yang tidak wajib (required)
    // agar tidak error jika dikirim kosong.
    $validator = Validator::make($request->all(), [
        'user_id'             => 'required', // Asumsi user_id boleh diubah/direlasikan ulang
        'nama_depan'          => 'required|string|max:100',
        'nama_belakang'       => 'nullable|string|max:100',
        
        // PENTING: unique:nama_tabel,nama_kolom,id_pengecualian
        // Pastikan nama tabel di database benar (misal: 'staf' atau 'stafs')
        'nomor_induk_pegawai' => 'nullable|string|max:30|unique:staf,nomor_induk_pegawai,' . $id,
        
        'unit_kerja'          => 'nullable|string|max:100',
        'jabatan'             => 'nullable|string|max:100',
        'alamat_staf'         => 'nullable|string',
        'nomor_telepon'       => 'nullable|string|max:20',
        'status_keaktifan'    => 'required|in:Aktif,Non-Aktif',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 3. Lakukan Update
    // (Kita tidak mengupdate 'id' karena primary key sebaiknya tidak diubah)
    $staf->update([
        'user_id'             => $request->user_id,
        'nama_depan'          => $request->nama_depan,
        'nama_belakang'       => $request->nama_belakang,
        'nomor_induk_pegawai' => $request->nomor_induk_pegawai,
        'unit_kerja'          => $request->unit_kerja,
        'jabatan'             => $request->jabatan,
        'alamat_staf'         => $request->alamat_staf,
        'nomor_telepon'       => $request->nomor_telepon,
        'status_keaktifan'    => $request->status_keaktifan, // Typo diperbaiki disini
    ]);

    return response()->json([
        'message' => 'Data Staf berhasil diperbarui',
        'data'    => $staf
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staf $staf)
    {
        $staf = Staf::find($id);

    if (!$staf) {
        return response()->json([
            'message' => 'Data Staf tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $staf->delete();

        return response()->json([
            'message' => 'Data Staf berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya Staf ini datanya masih dipakai di tabel lain
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Data Staf ini masih terhubung dengan data lain (User/Jadwal/dll).',
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
