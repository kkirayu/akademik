<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KrsMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class HasilStudiController extends Controller
{
    public function lihatKhs(Request $request)
    {
        $user = $request->user();
        
        if (!$user->mahasiswa) {
            return response()->json(['message' => 'Akses ditolak. Bukan Mahasiswa.'], 403);
        }

        $mahasiswaId = $user->mahasiswa->id;
        $tahunId = $request->tahun_akademik_id;

        if (!$tahunId) {
            return response()->json(['message' => 'Parameter tahun_akademik_id wajib diisi'], 400);
        }

        $krsData = KrsMahasiswa::with(['kelas.mataKuliah', 'tahunAkademik'])
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->where('tahun_akademik_id', $tahunId)
                    ->get();

        if ($krsData->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data KRS untuk semester ini'], 404);
        }

        $totalSks = 0;
        $totalBobot = 0;

        $detailNilai = [];

        foreach ($krsData as $krs) {
            $sks = $krs->kelas->mataKuliah->sks;
            $nilaiHuruf = $krs->nilai_akhir;
            $nilaiAngka = $this->konversiNilai($nilaiHuruf);

            if ($nilaiAngka !== null) {
                $totalSks += $sks;
                $totalBobot += ($sks * $nilaiAngka);
            }

            $detailNilai[] = [
                'kode_mk' => $krs->kelas->mataKuliah->kode_mk,
                'nama_mk' => $krs->kelas->mataKuliah->nama_mk,
                'sks'     => $sks,
                'nilai_huruf' => $nilaiHuruf ?? 'Belum Dinilai',
                'nilai_angka' => $nilaiAngka ?? 0
            ];
        }

        $ips = ($totalSks > 0) ? round($totalBobot / $totalSks, 2) : 0;

        return response()->json([
            'success' => true,
            'mahasiswa' => $user->mahasiswa->nama_lengkap,
            'semester'  => $krsData[0]->tahunAkademik->nama_tahun,
            'ips_semester_ini' => $ips,
            'total_sks_diambil' => $totalSks,
            'detail_mata_kuliah' => $detailNilai
        ]);
    }

    public function lihatTranskrip(Request $request)
    {
        $user = $request->user();
        if (!$user->mahasiswa) return response()->json(['message' => 'Akses ditolak'], 403);

        $krsData = KrsMahasiswa::with(['kelas.mataKuliah'])
                    ->where('mahasiswa_id', $user->mahasiswa->id)
                    ->whereNotNull('nilai_akhir') 
                    ->get();

        $totalSks = 0;
        $totalBobot = 0;

        foreach ($krsData as $krs) {
            $sks = $krs->kelas->mataKuliah->sks;
            $nilaiAngka = $this->konversiNilai($krs->nilai_akhir);
            
            $totalSks += $sks;
            $totalBobot += ($sks * $nilaiAngka);
        }

        $ipk = ($totalSks > 0) ? round($totalBobot / $totalSks, 2) : 0;

        return response()->json([
            'success' => true,
            'mahasiswa' => $user->mahasiswa->nama_lengkap,
            'nim' => $user->mahasiswa->nim,
            'ipk_kumulatif' => $ipk,
            'total_sks_lulus' => $totalSks,
        ]);
    }

    private function konversiNilai($huruf) {
        return match ($huruf) {
            'A'  => 4.00,
            'A-' => 3.75,
            'B+' => 3.50,
            'B'  => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C'  => 2.00,
            'D'  => 1.00,
            'E'  => 0.00,
            default => null, 
        };
    }
}
