<?php

namespace App\Http\Controllers;

use App\Models\MasterSesiWaktu;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MasterSesiWaktuController extends Controller
{
    public function index() {
        return response()->json(['success'=>true, 'data'=>MasterSesiWaktu::orderBy('sesi_ke')->get()]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'sesi_ke'     => 'required|integer|unique:master_sesi_waktus,sesi_ke',
            'jam_mulai'   => 'required|date_format:H:i', // Format jam:menit
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $sesi = MasterSesiWaktu::create($request->all());
        return response()->json(['success'=>true, 'data'=>$sesi], 201);
    }
}