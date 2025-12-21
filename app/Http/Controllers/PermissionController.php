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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        //
    }
}
