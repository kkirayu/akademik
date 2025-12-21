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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
