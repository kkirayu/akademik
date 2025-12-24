<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles = Role::all();
        return response()->json([
            'message' => 'Daftar Role berhasil diambil',
            'data' => $roles
        ], 200);
    }

    public function create()
    {
        //
    }

public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create([
            'nama_role' => $request->nama_role,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'message' => 'Role berhasil dibuat',
            'data' => $role
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $role = Role::find($id);

    // 2. Cek apakah data ditemukan
    if (!$role) {
        return response()->json([
            'message' => 'Role tidak ditemukan'
        ], 404);
    }

    // 3. Validasi Input
    $validator = Validator::make($request->all(), [
        // Format: unique:nama_tabel,nama_kolom,id_pengecualian
        'nama_role' => 'required|string|max:50|unique:roles,nama_role,' . $id,
        'deskripsi' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 4. Lakukan Update
    $role->update([
        'nama_role' => $request->nama_role,
        'deskripsi' => $request->deskripsi,
    ]);

    return response()->json([
        'message' => 'Role berhasil diperbarui',
        'data'    => $role
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role = Role::find($id);

    if (!$role) {
        return response()->json([
            'message' => 'Role tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $role->delete();

        return response()->json([
            'message' => 'Role berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya Role ini masih digunakan oleh User atau memiliki Permission terkait
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. Role ini sedang digunakan oleh User atau terhubung dengan Permission.',
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