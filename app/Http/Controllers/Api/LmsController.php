<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\Storage; // Tidak butuh Storage facade lagi

class LmsController extends Controller
{
    // --- FITUR MATERI (DOSEN) ---

    public function uploadMateri(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul_materi' => 'required',
            'file_materi' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        // --- UBAH DISINI: Upload ke Cloudinary ---
        // 'materi' adalah nama folder di Cloudinary
        $uploadedFile = $request->file('file_materi')->storeOnCloudinary('materi');
        
        // Ambil URL HTTPS lengkap (contoh: https://res.cloudinary.com/...)
        $linkFile = $uploadedFile->getSecurePath();
        // Ambil Public ID jika mau fitur delete (opsional): $uploadedFile->getPublicId();

        // Simpan ke DB (Isi file_path langsung dengan Link Cloudinary)
        $id = DB::table('materi_kuliah')->insertGetId([
            'kelas_id' => $request->kelas_id,
            'judul_materi' => $request->judul_materi,
            'deskripsi' => $request->deskripsi,
            'file_path' => $linkFile, // Simpan Link Full
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Materi PDF Berhasil Diupload ke Cloud', 'data_id' => $id]);
    }

    public function listMateri($kelas_id)
    {
        $materi = DB::table('materi_kuliah')->where('kelas_id', $kelas_id)->get();
        
        // --- UBAH DISINI ---
        // Tidak perlu manual generate URL lagi, karena di DB sudah HTTPS
        // Kita langsung return saja datanya
        
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
            'file_soal' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        $linkSoal = null;
        
        // Cek jika ada file soal diupload
        if ($request->hasFile('file_soal')) {
            // Upload Cloudinary
            $uploadedFile = $request->file('file_soal')->storeOnCloudinary('soal_tugas');
            $linkSoal = $uploadedFile->getSecurePath();
        }

        DB::table('tugas_kuliah')->insert([
            'kelas_id' => $request->kelas_id,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'file_soal_path' => $linkSoal, // Simpan Link Full / Null
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
            'file_jawaban' => 'required|file|mimes:pdf|max:10240',
        ]);

        $user = $request->user();
        if (!$user->mahasiswa) return response()->json(['message' => 'Hanya mahasiswa bisa upload'], 403);

        // Cek Deadline
        $tugas = DB::table('tugas_kuliah')->where('id', $request->tugas_id)->first();
        if (Carbon::now()->greaterThan($tugas->deadline)) {
            return response()->json(['message' => 'Maaf, waktu pengumpulan sudah habis (Deadline Lewat).'], 400);
        }

        // --- UBAH DISINI: Upload Jawaban ke Cloudinary ---
        $uploadedFile = $request->file('file_jawaban')->storeOnCloudinary('jawaban_tugas');
        $linkJawaban = $uploadedFile->getSecurePath();

        // Simpan / Update Submission
        DB::table('pengumpulan_tugas')->updateOrInsert(
            ['tugas_id' => $request->tugas_id, 'mahasiswa_id' => $user->mahasiswa->id],
            [
                'file_jawaban_path' => $linkJawaban, // Simpan Link Full
                'waktu_pengumpulan' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tugas Berhasil Dikumpulkan ke Cloud!']);
    }
    
    // Dosen Menilai Tugas (Bonus)
    // Tidak ada perubahan di fungsi ini karena cuma update nilai (angka/text)
    public function nilaiTugas(Request $request, $submission_id)
    {
        $request->validate(['nilai' => 'required|numeric|min:0|max:100']);
        
        DB::table('pengumpulan_tugas')
            ->where('id', $submission_id)
            ->update(['nilai' => $request->nilai, 'catatan_dosen' => $request->catatan]);

        return response()->json(['success' => true, 'message' => 'Nilai Disimpan']);
    }
}