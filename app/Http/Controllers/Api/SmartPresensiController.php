<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RealisasiPerkuliahan;
use App\Models\KrsMahasiswa; // Pastikan model ini ada atau sesuaikan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SmartPresensiController extends Controller
{

    public function generateKode(Request $request, $realisasi_id)
    {
        // Validasi: Pastikan yang akses adalah dosen yang mengajar

        $realisasi = RealisasiPerkuliahan::find($realisasi_id);
        if (!$realisasi) return response()->json(['message' => 'Pertemuan tidak ditemukan'], 404);

        $kode = strtoupper(substr(md5(time()), 0, 6)); 
        
        $realisasi->update([
            'kode_presensi' => $kode,
            'batas_waktu_presensi' => Carbon::now()->addMinutes(15)
        ]);

        return response()->json([
            'success' => true,
            'kode_akses' => $kode,
            'expired_at' => $realisasi->batas_waktu_presensi,
            'message' => 'Kode Presensi Dibuat. Berlaku 15 Menit.'
        ]);
    }

    /**
     * MAHASISWA: Input Kode untuk Absen
     */
    public function submitPresensi(Request $request)
    {
        $request->validate([
            'kode_akses' => 'required',
            'realisasi_id' => 'required|exists:realisasi_perkuliahan,id'
        ]);

        $user = $request->user();
        $mahasiswa = $user->mahasiswa; 

        $realisasi = RealisasiPerkuliahan::find($request->realisasi_id);

        if ($realisasi->kode_presensi !== $request->kode_akses) {
            return response()->json(['message' => 'Kode Salah!'], 400);
        }

        if (Carbon::now()->greaterThan($realisasi->batas_waktu_presensi)) {
            return response()->json(['message' => 'Kode Kadaluarsa. Silakan lapor dosen.'], 400);
        }

        $cekKrs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                    ->where('kelas_id', $realisasi->kelas_id)->exists();
        if(!$cekKrs) return response()->json(['message' => 'Anda tidak terdaftar di kelas ini'], 403);

        $sudahAbsen = DB::table('presensi_mahasiswa')
                        ->where('realisasi_id', $realisasi->id)
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->exists();
        
        if ($sudahAbsen) {
            return response()->json(['message' => 'Anda sudah melakukan presensi sebelumnya.'], 409);
        }
        
        DB::table('presensi_mahasiswa')->insert([
            'realisasi_id' => $realisasi->id,
            'mahasiswa_id' => $mahasiswa->id,
            'waktu_presensi' => Carbon::now(),
            'status' => 'Hadir',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Presensi Berhasil Tercatat!']);
    }
}