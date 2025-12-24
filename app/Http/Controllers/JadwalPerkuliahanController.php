<?php

namespace App\Http\Controllers;

use App\Models\JadwalPerkuliahan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class JadwalPerkuliahanController extends Controller
{
   public function index() {
        $jadwal = JadwalPerkuliahan::with(['kelas.mataKuliah', 'dosen', 'ruangan', 'sesi'])->get();
        return response()->json(['success'=>true, 'data'=>$jadwal]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'kelas_id'             => 'required|exists:kelas,id',
            'dosen_id'             => 'required|exists:dosens,id',
            'ruangan_id'           => 'required|exists:master_ruangans,id',
            'master_sesi_waktu_id' => 'required|exists:master_sesi_waktus,id',
            'hari'                 => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        // --- CEK BENTROK RUANGAN ---
        $cekRuangan = JadwalPerkuliahan::where('hari', $request->hari)
            ->where('master_sesi_waktu_id', $request->master_sesi_waktu_id)
            ->where('ruangan_id', $request->ruangan_id)
            ->exists();
        
        if ($cekRuangan) {
            return response()->json(['success'=>false, 'message'=>'Gagal: Ruangan sudah terpakai di sesi ini.'], 409);
        }

        // --- CEK BENTROK DOSEN ---
        $cekDosen = JadwalPerkuliahan::where('hari', $request->hari)
            ->where('master_sesi_waktu_id', $request->master_sesi_waktu_id)
            ->where('dosen_id', $request->dosen_id)
            ->exists();

        if ($cekDosen) {
            return response()->json(['success'=>false, 'message'=>'Gagal: Dosen sudah mengajar di kelas lain pada sesi ini.'], 409);
        }

        $jadwal = JadwalPerkuliahan::create($request->all());
        return response()->json(['success'=>true, 'data'=>$jadwal], 201);
    }

    public function destroy($id) {
        $jadwal = JadwalPerkuliahan::find($id);
        if($jadwal) {
            $jadwal->delete();
            return response()->json(['success'=>true, 'message'=>'Jadwal Dihapus']);
        }
        return response()->json(['message'=>'Not Found'], 404);
    }   
}
