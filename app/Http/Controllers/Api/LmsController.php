<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
// --- IMPORT PENTING ---
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; 

class LmsController extends Controller
{
    // ... (method uploadMateri dan listMateri biarkan saja jika sudah diperbaiki) ...

    // --- FITUR TUGAS (DOSEN & MAHASISWA) ---

    /**
     * Dosen Buat Tugas (Create)
     * URL: POST /api/lms/tugas
     */
    public function createTugas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul_tugas' => 'required',
            'deadline' => 'required|date',
            'file_soal' => 'nullable|file|mimes:pdf|max:5120' // Opsional, max 5MB
        ]);

        $linkSoal = null;

        // Cek apakah ada file soal yang diupload?
        if ($request->hasFile('file_soal')) {
            // Upload Manual pakai Facade (Anti-Gagal di Vercel)
            $uploadedFileUrl = Cloudinary::upload($request->file('file_soal')->getRealPath(), [
                'folder' => 'soal_tugas'
            ])->getSecurePath();
            
            $linkSoal = $uploadedFileUrl;
        }

        DB::table('tugas_kuliah')->insert([
            'kelas_id' => $request->kelas_id,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'file_soal_path' => $linkSoal, // Simpan Link HTTPS atau Null
            'deadline' => $request->deadline,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dibuat']);
    }

    /**
     * Mahasiswa Kumpul Tugas (Submit)
     * URL: POST /api/lms/submit-tugas
     */
    public function submitTugas(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tugas_kuliah,id',
            'file_jawaban' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        $user = $request->user();
        if (!$user->mahasiswa) {
            return response()->json(['message' => 'Hanya mahasiswa bisa upload tugas'], 403);
        }

        // 1. Cek Deadline
        $tugas = DB::table('tugas_kuliah')->where('id', $request->tugas_id)->first();
        if (Carbon::now()->greaterThan($tugas->deadline)) {
            return response()->json(['message' => 'Maaf, waktu pengumpulan sudah habis (Deadline Lewat).'], 400);
        }

        // 2. Upload Jawaban ke Cloudinary (Pakai Facade)
        $linkJawaban = Cloudinary::upload($request->file('file_jawaban')->getRealPath(), [
            'folder' => 'jawaban_tugas'
        ])->getSecurePath();

        // 3. Simpan / Update Submission di Database
        // Menggunakan updateOrInsert agar jika mahasiswa upload ulang, file lama tertimpa (revisi)
        DB::table('pengumpulan_tugas')->updateOrInsert(
            [
                'tugas_id' => $request->tugas_id, 
                'mahasiswa_id' => $user->mahasiswa->id
            ],
            [
                'file_jawaban_path' => $linkJawaban, // Simpan Link HTTPS
                'waktu_pengumpulan' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dikumpulkan ke Cloud!']);
    }

    // ... (method nilaiTugas biarkan saja) ...
}