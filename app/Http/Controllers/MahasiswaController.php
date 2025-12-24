<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['user', 'prodi']);

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('nama_lengkap', 'LIKE', "%{$keyword}%")
                  ->orWhere('nim', 'LIKE', "%{$keyword}%");
            });
        }

        if ($request->has('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }
        $perPage = $request->per_page ?? 10;
        
        $mahasiswa = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Mahasiswa',
            'data'    => $mahasiswa
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. Validasi Gabungan (User + Mahasiswa)
        $validator = Validator::make($request->all(), [
            // Validasi untuk tabel Users
            'username'      => 'required|string|unique:users,username',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role_id'       => 'required|exists:roles,id', // Pastikan ID Role Mahasiswa dikirim

            // Validasi untuk tabel Mahasiswas
            'nama_lengkap'  => 'required|string|max:255',
            'nim'           => 'required|unique:mahasiswas,nim',
            'angkatan'      => 'required|integer',
            'prodi_id'      => 'required|exists:program_studis,id',
            'dosen_wali_id' => 'nullable|exists:dosens,id',
            'alamat'        => 'nullable|string',
            'nomor_telepon' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Mulai Transaksi Database
        DB::beginTransaction();

        try {
            // A. Buat User dulu
            $user = User::create([
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => Hash::make($request->password), // Enkripsi password
                'role_id'   => $request->role_id,
                'is_active' => true,
            ]);

            // B. Buat Mahasiswa (Pakai ID dari user yang baru dibuat)
            $mahasiswa = Mahasiswa::create([
                'user_id'          => $user->id,
                'nama_lengkap'     => $request->nama_lengkap,
                'nim'              => $request->nim,
                'angkatan'         => $request->angkatan,
                'prodi_id'         => $request->prodi_id,
                'dosen_wali_id'    => $request->dosen_wali_id,
                'alamat'           => $request->alamat,
                'nomor_telepon'    => $request->nomor_telepon,
                'status_mahasiswa' => 'Aktif',
            ]);

            // Jika sampai sini sukses, simpan permanen
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa dan Akun User Berhasil Dibuat',
                'data'    => $mahasiswa
            ], 201);

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan database
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lihat detail 1 mahasiswa
     */
    public function show($id)
    {
        $mahasiswa = Mahasiswa::with(['user', 'prodi'])->find($id);

        if ($mahasiswa) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Data Mahasiswa',
                'data'    => $mahasiswa
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Mahasiswa Tidak Ditemukan',
        ], 404);
    }

    /**
     * Update Data Mahasiswa
     */
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa Tidak Ditemukan'], 404);
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'nama_lengkap'  => 'required|string|max:255',
            'nim'           => 'required|unique:mahasiswas,nim,' . $id, // Abaikan unik ID ini
            'prodi_id'      => 'required|exists:program_studis,id',
            'angkatan'      => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Update data mahasiswa saja
        $mahasiswa->update([
            'nama_lengkap'     => $request->nama_lengkap,
            'nim'              => $request->nim,
            'angkatan'         => $request->angkatan,
            'prodi_id'         => $request->prodi_id,
            'dosen_wali_id'    => $request->dosen_wali_id,
            'alamat'           => $request->alamat,
            'nomor_telepon'    => $request->nomor_telepon,
            'status_mahasiswa' => $request->status_mahasiswa ?? $mahasiswa->status_mahasiswa,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Mahasiswa Berhasil Diupdate',
            'data'    => $mahasiswa
        ], 200);
    }

    /**
     * Hapus Mahasiswa (Otomatis hapus User karena cascade di database)
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Data Tidak Ditemukan'], 404);
        }

        // Karena di database ada onDelete('cascade') pada User -> Mahasiswa,
        // Lebih aman kita hapus User-nya, maka Mahasiswanya ikut terhapus.
        $user = User::find($mahasiswa->user_id);
        
        if ($user) {
            $user->delete(); // Ini akan menghapus data di tabel users DAN mahasiswas
        } else {
            // Jaga-jaga jika user sudah hilang duluan
            $mahasiswa->delete(); 
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Mahasiswa dan Akun User Berhasil Dihapus',
        ], 200);
    }
}
