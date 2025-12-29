<?php

namespace App\Http\Controllers;

use App\Models\RealisasiPerkuliahan;
use Illuminate\Http\Request;

class RealisasiPerkuliahanController extends Controller
{
    public function index()
    {
        $data = RealisasiPerkuliahan::with(['ruangan', 'dosen'])->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_aktual' => 'required|date',
            'jam_mulai_aktual' => 'required',
            'jam_selesai_aktual' => 'required',
            'ruangan_id' => 'required|exists:master_ruangans,id',
            'dosen_pengajar_id' => 'required|exists:dosens,id',
            'topik_pembahasan' => 'nullable|string',
            'status_pertemuan' => 'required|in:Terlaksana,Dibatalkan,Pengganti,Kosong',
        ]);

        $realisasi = RealisasiPerkuliahan::create($validated);

        return response()->json([
            'success' => true, 
            'message' => 'Jurnal perkuliahan berhasil disimpan', 
            'data' => $realisasi
        ]);
    }

    public function show($id)
    {
        $realisasi = RealisasiPerkuliahan::findOrFail($id);
        return response()->json(['success' => true, 'data' => $realisasi]);
    }
}