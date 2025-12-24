<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'is_active'=> true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil',
            'data'    => $user,
            'token'   => $token
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user->is_active) {
            return response()->json(['message' => 'Akun Anda dinonaktifkan'], 403);
        }

        $user->update(['last_login' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('role');

        $detail = null;
        if ($user->role && $user->role->nama_role === 'Mahasiswa') {
            $detail = $user->mahasiswa;
        } elseif ($user->role && $user->role->nama_role === 'Dosen') {
            $detail = $user->dosen; 
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'token'   => $token,
            'token_type' => 'Bearer',
            'user'    => $user,
            'detail_profil' => $detail 
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('role');
        
        if ($user->role->nama_role === 'Mahasiswa') $user->load('mahasiswa');
        if ($user->role->nama_role === 'Dosen') $user->load('dosen');

        return response()->json(['success' => true, 'data' => $user]);
    }
}
