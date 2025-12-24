<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::with('prodi');
        if ($request->has('keyword')) {
            $query->where('nama_mk', 'LIKE', "%{$request->keyword}%")
                  ->orWhere('kode_mk', 'LIKE', "%{$request->keyword}%");
        }

        $mk = $query->paginate(10); 

        return response()->json(['success' => true, 'data' => $mk]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prodi_id'    => 'required|exists:program_studis,id',
            'kode_mk'     => 'required|unique:mata_kuliahs,kode_mk',
            'nama_mk'     => 'required|string',
            'sks'         => 'required|integer',
            'semester_mk' => 'required|integer',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $mk = MataKuliah::create($request->all());
        return response()->json(['success' => true, 'data' => $mk], 201);
    }

    public function show($id)
    {
        $mk = MataKuliah::with('prodi')->find($id);
        return $mk ? response()->json(['success' => true, 'data' => $mk]) 
                   : response()->json(['success' => false, 'message' => 'Not Found'], 404);
    }

    public function update(Request $request, $id)
    {
        $mk = MataKuliah::find($id);
        if (!$mk) return response()->json(['message' => 'Not Found'], 404);

        $mk->update($request->all());
        return response()->json(['success' => true, 'data' => $mk]);
    }

    public function destroy($id)
    {
        $mk = MataKuliah::find($id);
        if ($mk) {
            $mk->delete();
            return response()->json(['success' => true, 'message' => 'Mata Kuliah Dihapus']);
        }
        return response()->json(['message' => 'Not Found'], 404);
    }
}
