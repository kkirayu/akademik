<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index() {
        // Load MK dan Tahun supaya datanya jelas
        $kelas = Kelas::with(['mataKuliah', 'tahunAkademik'])->latest()->get();
        return response()->json(['success'=>true, 'data'=>$kelas]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'mata_kuliah_id'    => 'required|exists:mata_kuliahs,id',
            'nama_kelas'        => 'required|string|max:50', // Contoh: A, B, Reguler Pagi
            'kuota'             => 'required|integer|min:1',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $kelas = Kelas::create($request->all());
        return response()->json(['success'=>true, 'data'=>$kelas], 201);
    }

    public function show($id) {
        $kelas = Kelas::with(['mataKuliah', 'tahunAkademik'])->find($id);
        return $kelas ? response()->json(['success'=>true, 'data'=>$kelas]) 
                      : response()->json(['message'=>'Not Found'], 404);
    }
}
