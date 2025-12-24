<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permission = Permission::all();
        return response()->json([
            'message' => 'Daftar Role berhasil diambil',
            'data' => $permission
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
            'nama_aksi' => 'required|string|<max:100>|unique:permission,nama_aksi',
            'nama_menu' => 'required|string|<max:100>',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // <-- Tanda kurung kurawal penutup (}) untuk blok 'if' hilang di kode Anda sebelumnya.
        // KODE DI SINI HANYA AKAN DIJALANKAN JIKA VALIDASI BERHASIL

        $permission = Permission::create([
            'nama_aksi' => $request->nama_aksi,
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'message' => 'Permission berhasil dibuat',
            'data' => $permission
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $permission = Permission::find($id);

    // 2. Cek apakah data ada
    if (!$permission) {
        return response()->json([
            'message' => 'Data Permission tidak ditemukan'
        ], 404);
    }

    // 3. Validasi Input
    $validator = Validator::make($request->all(), [
        // Format: unique:nama_tabel,nama_kolom,id_yang_dikecualikan
        'nama_aksi' => 'required|string|max:100|unique:permission,nama_aksi,' . $id,
        'nama_menu' => 'required|string|max:100',
        'deskripsi' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 4. Lakukan Update
    $permission->update([
        'nama_aksi' => $request->nama_aksi,
        'nama_menu' => $request->nama_menu,
        'deskripsi' => $request->deskripsi,
    ]);

    return response()->json([
        'message' => 'Permission berhasil diperbarui',
        'data'    => $permission
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission = Permission::find($id);

    if (!$permission) {
        return response()->json([
            'message' => 'Data Permission tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $permission->delete();

        return response()->json([
            'message' => 'Permission berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya permission ini sedang dipakai oleh Role tertentu
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Permission ini sedang digunakan oleh Role atau User lain.',
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
