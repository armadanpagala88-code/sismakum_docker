<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriDokumen;
use Illuminate\Http\Request;

class KategoriDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = KategoriDokumen::orderBy('order', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
        
        return response()->json($kategori);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Convert is_active to boolean
        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
            ]);
        }

        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:kategori_dokumen,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $kategori = KategoriDokumen::create($validated);
        return response()->json($kategori, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kategori = KategoriDokumen::findOrFail($id);
        return response()->json($kategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = KategoriDokumen::findOrFail($id);

        // Convert is_active to boolean
        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
            ]);
        }

        $validated = $request->validate([
            'kode' => 'sometimes|required|string|max:50|unique:kategori_dokumen,kode,' . $id,
            'nama' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $kategori->update($validated);
        return response()->json($kategori);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategori = KategoriDokumen::findOrFail($id);
        
        // Check if kategori is used in pengusulan
        if ($kategori->pengusulan()->count() > 0) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan dalam pengusulan'
            ], 422);
        }
        
        $kategori->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
