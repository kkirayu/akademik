<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class LmsController extends Controller
{
    // --- FITUR MATERI (DOSEN) ---

    public function uploadMateri(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul_materi' => 'required',
            'file_materi' => 'required|file|mimes:pdf|max:10240', // Max 10MB, PDF only
        ]);

        // Simpan File ke Storage (folder: public/materi)
        $path = $request->file('file_materi')->store('materi', 'public');

        // Simpan ke DB
        $id = DB::table('materi_kuliah')->insertGetId([
            'kelas_id' => $request->kelas_id,
            'judul_materi' => $request->judul_materi,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path, // Path relatif
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Materi PDF Berhasil Diupload', 'data_id' => $id]);
    }

    public function listMateri($kelas_id)
    {
        $materi = DB::table('materi_kuliah')->where('kelas_id', $kelas_id)->get();
        // Tambahkan URL lengkap biar frontend gampang akses
        foreach($materi as $m) {
            $m->download_url = url('storage/' . $m->file_path);
        }
        return response()->json(['success' => true, 'data' => $materi]);
    }


    // --- FITUR TUGAS (DOSEN & MAHASISWA) ---

    // Dosen Buat Tugas
    public function createTugas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul_tugas' => 'required',
            'deadline' => 'required|date',
            'file_soal' => 'nullable|file|mimes:pdf|max:5120' // Opsional upload soal PDF
        ]);

        $path = null;
        if ($request->hasFile('file_soal')) {
            $path = $request->file('file_soal')->store('soal_tugas', 'public');
        }

        DB::table('tugas_kuliah')->insert([
            'kelas_id' => $request->kelas_id,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'file_soal_path' => $path,
            'deadline' => $request->deadline,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dibuat']);
    }

    // Mahasiswa Upload Tugas
    public function submitTugas(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tugas_kuliah,id',
            'file_jawaban' => 'required|file|mimes:pdf|max:10240', // PDF Only
        ]);

        $user = $request->user();
        if (!$user->mahasiswa) return response()->json(['message' => 'Hanya mahasiswa bisa upload'], 403);

        // Cek Deadline
        $tugas = DB::table('tugas_kuliah')->where('id', $request->tugas_id)->first();
        if (Carbon::now()->greaterThan($tugas->deadline)) {
            return response()->json(['message' => 'Maaf, waktu pengumpulan sudah habis (Deadline Lewat).'], 400);
        }

        // Upload File
        $path = $request->file('file_jawaban')->store('jawaban_tugas', 'public');

        // Simpan / Update Submission
        // Logic: Jika sudah pernah upload, replace file lama (Re-upload)
        DB::table('pengumpulan_tugas')->updateOrInsert(
            ['tugas_id' => $request->tugas_id, 'mahasiswa_id' => $user->mahasiswa->id],
            [
                'file_jawaban_path' => $path,
                'waktu_pengumpulan' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dikumpulkan!']);
    }
    
    // Dosen Menilai Tugas (Bonus)
    public function nilaiTugas(Request $request, $submission_id)
    {
        $request->validate(['nilai' => 'required|numeric|min:0|max:100']);
        
        DB::table('pengumpulan_tugas')
            ->where('id', $submission_id)
            ->update(['nilai' => $request->nilai, 'catatan_dosen' => $request->catatan]);

        return response()->json(['success' => true, 'message' => 'Nilai Disimpan']);
    }
}