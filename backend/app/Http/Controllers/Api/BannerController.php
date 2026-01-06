<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Public endpoint
    public function public()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($banners);
    }

    // Admin endpoints
    public function index()
    {
        $banners = Banner::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'url' => 'nullable|url|max:500',
                'order' => 'nullable|integer',
                'is_active' => 'boolean',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('banners', 'public');
            }

            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['order'] = $validated['order'] ?? 0;

            $banner = Banner::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil ditambahkan',
                'data' => $banner
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan banner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'url' => 'nullable|url|max:500',
                'order' => 'nullable|integer',
                'is_active' => 'boolean',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($banner->logo) {
                    Storage::disk('public')->delete($banner->logo);
                }
                $validated['logo'] = $request->file('logo')->store('banners', 'public');
            }

            $banner->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil diupdate',
                'data' => $banner->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate banner: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            
            // Delete logo
            if ($banner->logo) {
                Storage::disk('public')->delete($banner->logo);
            }
            
            $banner->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus banner: ' . $e->getMessage()
            ], 500);
        }
    }
}
