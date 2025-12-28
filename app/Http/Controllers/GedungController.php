<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query
        $query = Gedung::query();

        // 2. Fitur Pencarian (Opsional)
        // Kalau frontend kirim params ?search=Gedung A
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where('nama_gedung', 'LIKE', "%$keyword%")
                  ->orWhere('kode_gedung', 'LIKE', "%$keyword%")
                  ->orWhere('lokasi', 'LIKE', "%$keyword%");
        }

        // 3. Ambil Data (Bisa tambah ->paginate(10) kalau datanya ratusan)
        $gedungs = $query->orderBy('kode_gedung', 'asc')->get();

        // 4. Return JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data Gedung Berhasil Diambil',
            'total_data' => $gedungs->count(),
            'data' => $gedungs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Gedung $gedung)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gedung $gedung)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gedung $gedung)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gedung $gedung)
    {
        //
    }
}
