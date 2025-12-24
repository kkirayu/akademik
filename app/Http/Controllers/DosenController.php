<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Dosen = Dosen::all();
        return response()->json([
            'message' => 'Daftar Dosen berhasil diambil',
            'data' => $Dosen
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
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            // Cek apakah user_id ada di tabel users, dan belum dipakai di tabel dosens
            'user_id' => 'required|exists:users,id|unique:dosens,user_id',
            
            'nama_depan'      => 'required|string|max:100',
            'nama_belakang'   => 'nullable|string|max:100',
            'gelar_depan'     => 'nullable|string|max:50',
            'gelar_belakang'  => 'nullable|string|max:50',
            
            // Cek unik NIP di tabel dosens
            'nip_dosen'       => 'required|string|max:30|unique:dosens,nip_dosen',
            
            // Cek unik Email Institusi di tabel dosens (jika diisi)
            'email_institusi' => 'nullable|email|max:100|unique:dosens,email_institusi',
            
            'alamat_dosen'    => 'nullable|string',
            'nomor_telepon'   => 'nullable|string|max:20',
            'status_keaktifan'=> 'in:Aktif,Cuti,Pensiun', // Sesuai ENUM
        ]);

        // 2. Cek Error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 3. Simpan Data
        try {
            $dosen = Dosen::create($request->all());

            return response()->json([
                'message' => 'Data Dosen berhasil ditambahkan',
                'data'    => $dosen
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

   
    public function show(Dosen $dosen)
    {
        //
    }


    public function edit(Dosen $dosen)
    {
        //
    }


    public function update(Request $request, Dosen $dosen)
    {
        $validator = Validator::make($request->all(), [
           'user_id' => 'required|exists:users,id|unique:dosens,user_id',
            
            'nama_depan'      => 'required|string|max:100',
            'nama_belakang'   => 'nullable|string|max:100',
            'gelar_depan'     => 'nullable|string|max:50',
            'gelar_belakang'  => 'nullable|string|max:50',
            
           
            'nip_dosen'       => 'required|string|max:30|unique:dosens,nip_dosen',
            
            
            'email_institusi' => 'nullable|email|max:100|unique:dosens,email_institusi',
            
            'alamat_dosen'    => 'nullable|string',
            'nomor_telepon'   => 'nullable|string|max:20',
            'status_keaktifan'=> 'in:Aktif,Cuti,Pensiun', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $dosen->update($request->all());

        return response()->json([
            'message' => 'Data dosen berhasil diperbarui',
            'data'    => $dosen 
        ], 200);
    }
    

   
    public function destroy(Dosen $dosen)
    {
        $dosen = Dosen ::find($id);

        if (!$dosen) {
            return response()->json([
                'message' => 'Data dosen tidak ditemukan'
            ], 404);
        }

        $dosen->delete();

        return response()->json([
            'message' => 'Data dosen berhasil dihapus'
        ], 200);
    }
}
