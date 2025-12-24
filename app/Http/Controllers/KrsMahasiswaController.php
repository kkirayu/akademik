<?php

namespace App\Http\Controllers;

use App\Models\KrsMahasiswa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KrsMahasiswaController extends Controller
{
   public function index(Request $request)
    {
        $query = KrsMahasiswa::with(['mahasiswa', 'kelas.mataKuliah', 'kelas.jadwal.sesi', 'tahunAkademik']);

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        if ($request->has('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        return response()->json(['success' => true, 'data' => $query->latest()->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mahasiswa_id'      => 'required|exists:mahasiswas,id',
            'kelas_id'          => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kelasTarget = Kelas::with(['mataKuliah', 'jadwal'])->find($request->kelas_id);
        
        if (!$kelasTarget) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $cekDuplikat = KrsMahasiswa::where('mahasiswa_id', $request->mahasiswa_id)
            ->where('kelas_id', $request->kelas_id)
            ->exists();
        if ($cekDuplikat) {
            return response()->json(['success' => false, 'message' => 'Anda sudah mengambil kelas ini.'], 409);
        }

        $jumlahPendaftar = KrsMahasiswa::where('kelas_id', $request->kelas_id)->count();
        if ($jumlahPendaftar >= $kelasTarget->kuota) {
            return response()->json(['success' => false, 'message' => 'Gagal: Kuota kelas sudah penuh.'], 409);
        }

        $prasyarats = PrasyaratMk::where('mk_id', $kelasTarget->mata_kuliah_id)->get();

        foreach ($prasyarats as $syarat) {
            $sudahLulus = KrsMahasiswa::where('mahasiswa_id', $request->mahasiswa_id)
                ->whereHas('kelas', function($q) use ($syarat) {
                    $q->where('mata_kuliah_id', $syarat->mk_syarat_id);
                })
                ->whereIn('nilai_akhir', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D']) 
                ->exists();

            if (!$sudahLulus) {
                $mkSyarat = $syarat->syarat->nama_mk ?? 'Mata Kuliah Prasyarat';
                return response()->json([
                    'success' => false, 
                    'message' => "Gagal: Anda belum lulus prasyarat: $mkSyarat"
                ], 403);
            }
        }

        if ($kelasTarget->jadwal->isNotEmpty()) { 
            foreach ($kelasTarget->jadwal as $jadwalBaru) {
                $bentrok = KrsMahasiswa::where('mahasiswa_id', $request->mahasiswa_id)
                    ->where('tahun_akademik_id', $request->tahun_akademik_id)
                    ->whereHas('kelas.jadwal', function($q) use ($jadwalBaru) {
                        $q->where('hari', $jadwalBaru->hari)
                          ->where('master_sesi_waktu_id', $jadwalBaru->master_sesi_waktu_id);
                    })
                    ->exists();

                if ($bentrok) {
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal: Jadwal bentrok dengan mata kuliah lain di hari {$jadwalBaru->hari}."
                    ], 409);
                }
            }
        }

        try {
            DB::beginTransaction();

            $krs = KrsMahasiswa::create([
                'mahasiswa_id'      => $request->mahasiswa_id,
                'kelas_id'          => $request->kelas_id,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'status_approval'   => 'Menunggu', 
                'nilai_akhir'       => null
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'KRS Berhasil Diambil',
                'data' => $krs
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error Database: ' . $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $krs = KrsMahasiswa::with(['mahasiswa', 'kelas.mataKuliah'])->find($id);
        if ($krs) {
            return response()->json(['success' => true, 'data' => $krs]);
        }
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    public function update(Request $request, $id)
    {
        $krs = KrsMahasiswa::find($id);
        if (!$krs) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'status_approval' => 'in:Menunggu,Disetujui,Ditolak',
            'nilai_akhir'     => 'string|nullable|max:5' 
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $krs->update($request->only(['status_approval', 'nilai_akhir']));

        return response()->json(['success' => true, 'message' => 'Data KRS Diupdate', 'data' => $krs]);
    }

    public function destroy($id)
    {
        $krs = KrsMahasiswa::find($id);
        
        if (!$krs) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        if ($krs->nilai_akhir != null) {
            return response()->json(['message' => 'Gagal: Tidak bisa menghapus KRS yang sudah ada nilainya.'], 403);
        }

        $krs->delete();
        return response()->json(['success' => true, 'message' => 'KRS Berhasil Dibatalkan']);
    }
}