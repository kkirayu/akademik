<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; 

class LmsController extends Controller
{
    public function uploadMateri(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul_materi' => 'required',
            'file_link' => 'required|url', // Validasi berupa URL
        ]);

        $id = DB::table('materi_kuliah')->insertGetId([
            'kelas_id' => $request->kelas_id,
            'judul_materi' => $request->judul_materi,
            'deskripsi' => $request->deskripsi,
            'file_path' => $request->file_link, // Simpan link langsung
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Link Materi Berhasil Disimpan', 'data_id' => $id]);
    }

    // Buat Tugas (Sekarang menggunakan Link Soal)
    public function createTugas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul_tugas' => 'required',
            'deadline' => 'required|date',
            'file_soal_link' => 'nullable|url' // Validasi berupa URL
        ]);

        DB::table('tugas_kuliah')->insert([
            'kelas_id' => $request->kelas_id,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'file_soal_path' => $request->file_soal_link, // Simpan link langsung
            'deadline' => $request->deadline,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dibuat dengan Link Soal']);
    }

    public function listMateri($kelas_id)
    {
        $materi = DB::table('materi_kuliah')->where('kelas_id', $kelas_id)->get();
        return response()->json(['success' => true, 'data' => $materi]);
    }


    public function submitTugas(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tugas_kuliah,id',
            'file_jawaban' => 'required|file|mimes:pdf|max:10240',
        ]);

        $user = $request->user();
        if (!$user->mahasiswa) {
            return response()->json(['message' => 'Hanya mahasiswa bisa upload tugas'], 403);
        }

        // Cek Deadline
        $tugas = DB::table('tugas_kuliah')->where('id', $request->tugas_id)->first();
        if (Carbon::now()->greaterThan($tugas->deadline)) {
            return response()->json(['message' => 'Maaf, waktu pengumpulan sudah habis (Deadline Lewat).'], 400);
        }

        // Upload Jawaban
        $linkJawaban = Cloudinary::upload($request->file('file_jawaban')->getRealPath(), [
            'folder' => 'jawaban_tugas'
        ])->getSecurePath();

        //  Update Submission
        DB::table('pengumpulan_tugas')->updateOrInsert(
            [
                'tugas_id' => $request->tugas_id, 
                'mahasiswa_id' => $user->mahasiswa->id
            ],
            [
                'file_jawaban_path' => $linkJawaban,
                'waktu_pengumpulan' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dikumpulkan ke Cloud!']);
    }
    
    public function nilaiTugas(Request $request, $submission_id)
    {
        $request->validate(['nilai' => 'required|numeric|min:0|max:100']);
        
        DB::table('pengumpulan_tugas')
            ->where('id', $submission_id)
            ->update(['nilai' => $request->nilai, 'catatan_dosen' => $request->catatan]);

        return response()->json(['success' => true, 'message' => 'Nilai Disimpan']);
    }
}