<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KrsMahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenilaianController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Format Input
        $validator = Validator::make($request->all(), [
            'kelas_id'            => 'required|exists:kelas,id',
            'data_nilai'          => 'required|array', // Harus berupa Array (Daftar)
            'data_nilai.*.mahasiswa_id' => 'required|exists:mahasiswas,id',
            'data_nilai.*.nilai_huruf'  => 'required|in:A,A-,B+,B,B-,C+,C,D,E,T' // Validasi Nilai
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Cek apakah Kelas Valid
        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        // (Opsional) Cek apakah Dosen yang login berhak menilai kelas ini?
        // if ($request->user()->dosen->id != $kelas->jadwal->dosen_id) { ... }

        // 3. Proses Simpan Massal (Looping)
        DB::beginTransaction();
        try {
            $berhasil = 0;
            
            foreach ($request->data_nilai as $item) {
                // Cari data KRS mahasiswa tersebut di kelas ini
                $krs = KrsMahasiswa::where('kelas_id', $request->kelas_id)
                                   ->where('mahasiswa_id', $item['mahasiswa_id'])
                                   ->first();

                // Jika ketemu, update nilainya
                if ($krs) {
                    $krs->update([
                        'nilai_akhir' => $item['nilai_huruf'],
                        // Otomatis disetujui jika nilai sudah keluar
                        'status_approval' => 'Disetujui' 
                    ]);
                    $berhasil++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyimpan nilai untuk $berhasil mahasiswa.",
                'kelas_id' => $kelas->id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Lihat Rekap Nilai Satu Kelas
     */
    public function show($kelas_id)
    {
        $nilai = KrsMahasiswa::with('mahasiswa')
                    ->where('kelas_id', $kelas_id)
                    ->get(['id', 'mahasiswa_id', 'nilai_akhir']);
                    
        return response()->json(['success' => true, 'data' => $nilai]);
    }
}
