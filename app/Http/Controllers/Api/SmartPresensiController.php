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
    /**
     * DOSEN: Generate Kode Presensi untuk sesi ini
     */
    public function generateKode(Request $request, $realisasi_id)
    {
        // Validasi: Pastikan yang akses adalah dosen yang mengajar
        // (Logic auth user skip dulu biar simpel, asumsikan middleware role sudah jalan)

        $realisasi = RealisasiPerkuliahan::find($realisasi_id);
        if (!$realisasi) return response()->json(['message' => 'Pertemuan tidak ditemukan'], 404);

        // Generate Kode Acak 6 Digit
        $kode = strtoupper(substr(md5(time()), 0, 6)); 
        
        // Set expired 15 menit dari sekarang
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
        $mahasiswa = $user->mahasiswa; // Relasi dari User ke Mahasiswa

        $realisasi = RealisasiPerkuliahan::find($request->realisasi_id);

        // 1. Cek Kode Benar/Salah
        if ($realisasi->kode_presensi !== $request->kode_akses) {
            return response()->json(['message' => 'Kode Salah!'], 400);
        }

        // 2. Cek Expired
        if (Carbon::now()->greaterThan($realisasi->batas_waktu_presensi)) {
            return response()->json(['message' => 'Kode Kadaluarsa. Silakan lapor dosen.'], 400);
        }

        // 3. Cek Apakah Mahasiswa Benar Mengambil Kelas Ini? (Opsional tapi penting)
        // $cekKrs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
        //             ->where('kelas_id', $realisasi->kelas_id)->exists();
        // if(!$cekKrs) return response()->json(['message' => 'Anda tidak terdaftar di kelas ini'], 403);

        // 4. Cek Apakah Sudah Absen Sebelumnya?
        $sudahAbsen = DB::table('presensi_mahasiswa')
                        ->where('realisasi_id', $realisasi->id)
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->exists();
        
        if ($sudahAbsen) {
            return response()->json(['message' => 'Anda sudah melakukan presensi sebelumnya.'], 409);
        }

        // 5. Simpan Presensi
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