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
        //
    }
    public function destroy(User $user)
    {
        //
    }
}
