<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterRuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterRuanganController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => MasterRuangan::all()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_ruangan' => 'required|unique:master_ruangans,kode_ruangan',
            'nama_ruangan' => 'required',
            'kapasitas'    => 'required|integer',
            'jenis_ruangan'=> 'in:Kelas,Laboratorium,Auditorium',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $ruangan = MasterRuangan::create($request->all());
        return response()->json(['success' => true, 'data' => $ruangan], 201);
    }

    public function show($id)
    {
        $ruangan = MasterRuangan::find($id);
        return $ruangan ? response()->json(['success' => true, 'data' => $ruangan]) : response()->json(['message' => 'Not Found'], 404);
    }

    public function update(Request $request, $id)
    {
        $ruangan = MasterRuangan::find($id);
        if (!$ruangan) return response()->json(['message' => 'Not Found'], 404);
        $ruangan->update($request->all());
        return response()->json(['success' => true, 'data' => $ruangan]);
    }

    public function destroy($id)
    {
        $ruangan = MasterRuangan::find($id);
        if ($ruangan) {
            $ruangan->delete();
            return response()->json(['success' => true, 'message' => 'Berhasil Dihapus']);
        }
        return response()->json(['message' => 'Not Found'], 404);
    }
}
