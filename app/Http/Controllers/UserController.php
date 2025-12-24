<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        //
        $user = User::all();
        return response()->json([
            'message' => 'Daftar Role berhasil diambil',
            'data' => $user
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
{
    // 1. Validasi
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:50',
        'email'    => 'required|string|max:100',
        'password' => 'required|string',
        'role_id'  => 'required|string',
    ]);

    // 2. Cek jika gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    } 

    $user = User::create([
        'username' => $request->username,
        'email'    => $request->email,
        'password' => bcrypt($request->password), 
        'role_id'  => $request->role_id,
    ]);

    return response()->json([
        'message' => 'User berhasil dibuat',
        'data'    => $user
    ], 201);
}

    public function show(User $user)
    {
        //~
    }
    public function edit(User $user)
    {
        //
    }
    public function update(Request $request, User $user)
    {
        $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    // 2. Validasi Input
    $validator = Validator::make($request->all(), [
        // Unique ignore ID: unique:users,username,ID
        'username' => 'required|string|max:50|unique:users,username,' . $id,
        'email'    => 'required|string|email|max:100|unique:users,email,' . $id,
        // Password dibuat nullable (tidak wajib diisi saat edit)
        'password' => 'nullable|string|min:6', 
        'role_id'  => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    // 3. Siapkan data update
    // Kita buat array dulu, karena password butuh perlakuan khusus
    $dataToUpdate = [
        'username' => $request->username,
        'email'    => $request->email,
        'role_id'  => $request->role_id,
    ];

    // Cek apakah user mengirim password baru?
    // Jika ada isinya, kita hash dan masukkan ke array update. 
    // Jika kosong, biarkan password lama tetap ada (jangan di-update).
    if ($request->filled('password')) {
        $dataToUpdate['password'] = bcrypt($request->password);
    }

    // 4. Lakukan Update
    $user->update($dataToUpdate);

    return response()->json([
        'message' => 'User berhasil diperbarui',
        'data'    => $user
    ], 200);
    }
    public function destroy(User $user)
    {
        $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    try {
        // 2. Hapus data
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        // Error Code 23000: Integrity Constraint Violation
        // Artinya User ini datanya masih dipakai di tabel Staf/Mahasiswa/Log
        if ($e->getCode() == "23000") {
            return response()->json([
                'message' => 'Gagal menghapus. User ini masih terhubung dengan data Staf, Mahasiswa, atau data lainnya.',
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
