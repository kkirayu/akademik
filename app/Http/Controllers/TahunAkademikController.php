<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TahunAkademikController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => TahunAkademik::latest()->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_tahun'      => 'required|unique:tahun_akademiks,kode_tahun',
            'nama_tahun'      => 'required',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'in:Aktif,Selesai,Direncanakan'
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $ta = TahunAkademik::create($request->all());
        return response()->json(['success' => true, 'data' => $ta], 201);
    }

    public function show($id)
    {
        $ta = TahunAkademik::find($id);
        return $ta ? response()->json(['success' => true, 'data' => $ta]) : response()->json(['message' => 'Not Found'], 404);
    }

    public function update(Request $request, $id)
    {
        $ta = TahunAkademik::find($id);
        if (!$ta) return response()->json(['message' => 'Not Found'], 404);
        $ta->update($request->all());
        return response()->json(['success' => true, 'data' => $ta]);
    }

    public function destroy($id)
    {
        $ta = TahunAkademik::find($id);
        if ($ta) {
            $ta->delete();
            return response()->json(['success' => true, 'message' => 'Berhasil Dihapus']);
        }
        return response()->json(['message' => 'Not Found'], 404);
    }
}
