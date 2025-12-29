<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::with('user')->get();
        return response()->json(['success' => true, 'data' => $dosen]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'      => 'required|unique:users,username',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role_id'       => 'required', 
            
            'nama_depan'    => 'required|string',
            'nip_dosen'     => 'required|unique:dosens,nip_dosen',
            'status_keaktifan' => 'in:Aktif,Cuti,Pensiun',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        DB::beginTransaction(); // Mulai Transaksi
        try {
            // 1. Buat User
            $user = User::create([
                'username' => $request->username,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $request->role_id,
                'is_active'=> true,
            ]);

            // 2. Buat Dosen
            $dosen = Dosen::create([
                'user_id'         => $user->id,
                'nama_depan'      => $request->nama_depan,
                'nama_belakang'   => $request->nama_belakang,
                'gelar_depan'     => $request->gelar_depan,
                'gelar_belakang'  => $request->gelar_belakang,
                'nip_dosen'       => $request->nip_dosen,
                'email_institusi' => $request->email_institusi,
                'nomor_telepon'   => $request->nomor_telepon,
                'status_keaktifan'=> $request->status_keaktifan ?? 'Aktif',
            ]);

            DB::commit();
            return response()->json(['success' => true, 'data' => $dosen], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $dosen = Dosen::with('user')->find($id);
        return $dosen ? response()->json(['success' => true, 'data' => $dosen]) 
                      : response()->json(['success' => false, 'message' => 'Not Found'], 404);
    }

    public function update(Request $request, $id)
    {
        $dosen = Dosen::find($id);
        if (!$dosen) return response()->json(['message' => 'Not Found'], 404);

        $dosen->update($request->except(['user_id', 'nip_dosen'])); 
        return response()->json(['success' => true, 'data' => $dosen]);
    }

    public function destroy($id)
    {
        $dosen = Dosen::find($id);
        if (!$dosen) return response()->json(['message' => 'Not Found'], 404);

        $user = User::find($dosen->user_id);
        if ($user) $user->delete();
        else $dosen->delete();

        return response()->json(['success' => true, 'message' => 'Dosen Berhasil Dihapus']);
    }
}
