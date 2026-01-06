<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    // Public endpoint
    public function public()
    {
        $berita = Berita::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('order', 'asc')
            ->limit(10)
            ->get();

        return response()->json($berita);
    }

    // Get headline berita
    public function headline()
    {
        $berita = Berita::where('is_published', true)
            ->where('is_headline', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('order', 'asc')
            ->limit(5)
            ->get();

        return response()->json($berita);
    }

    public function showPublic($slug)
    {
        $berita = Berita::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Increment views
        $berita->increment('views');

        return response()->json($berita);
    }

    // Admin endpoints
    public function index()
    {
        $berita = Berita::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($berita);
    }

    public function store(Request $request)
    {
        // Convert is_published to boolean
        if ($request->has('is_published')) {
            $isPublished = $request->is_published;
            if (is_string($isPublished)) {
                $isPublished = $isPublished === '1' || $isPublished === 'true';
            }
            $request->merge([
                'is_published' => (bool) $isPublished
            ]);
        }

        // Convert is_headline to boolean
        if ($request->has('is_headline')) {
            $isHeadline = $request->is_headline;
            if (is_string($isHeadline)) {
                $isHeadline = $isHeadline === '1' || $isHeadline === 'true';
            }
            $request->merge([
                'is_headline' => (bool) $isHeadline
            ]);
        }
        
        $validated = $request->validate([
            'judul' => 'required|string',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'penulis' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'is_headline' => 'boolean',
            'published_at' => 'nullable|date',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['judul']);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        if ($validated['is_published'] && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $berita = Berita::create($validated);
        return response()->json($berita, 201);
    }

    public function show($id)
    {
        $berita = Berita::findOrFail($id);
        return response()->json($berita);
    }

    public function update(Request $request, $id)
    {
        try {
            $berita = Berita::findOrFail($id);

            // Log incoming request for debugging
            \Log::info('Update berita request:', [
                'id' => $id,
                'has_judul' => $request->has('judul'),
                'judul_value' => $request->input('judul'),
                'has_isi' => $request->has('isi'),
                'isi_length' => $request->has('isi') ? strlen($request->input('isi')) : 0,
                'has_is_published' => $request->has('is_published'),
                'is_published_value' => $request->input('is_published'),
                'has_is_headline' => $request->has('is_headline'),
                'is_headline_value' => $request->input('is_headline'),
                'all_keys' => array_keys($request->all())
            ]);

            // Convert is_published to boolean
            if ($request->has('is_published')) {
                $isPublished = $request->is_published;
                if (is_string($isPublished)) {
                    $isPublished = $isPublished === '1' || $isPublished === 'true';
                }
                $request->merge([
                    'is_published' => (bool) $isPublished
                ]);
            } else {
                // Keep existing value if not provided
                $request->merge(['is_published' => $berita->is_published ?? false]);
            }

            // Convert is_headline to boolean
            if ($request->has('is_headline')) {
                $isHeadline = $request->is_headline;
                if (is_string($isHeadline)) {
                    $isHeadline = $isHeadline === '1' || $isHeadline === 'true';
                }
                $request->merge([
                    'is_headline' => (bool) $isHeadline
                ]);
            } else {
                // Keep existing value if not provided
                $request->merge(['is_headline' => $berita->is_headline ?? false]);
            }

            // Validate with better error messages
            try {
                $validated = $request->validate([
                    'judul' => 'required|string',
                    'isi' => 'required|string',
                    'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'penulis' => 'nullable|string|max:255',
                    'is_published' => 'boolean',
                    'is_headline' => 'boolean',
                    'published_at' => 'nullable|date',
                    'order' => 'nullable|integer',
                ], [
                    'judul.required' => 'Judul berita wajib diisi',
                    'isi.required' => 'Isi berita wajib diisi',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);
                throw $e;
            }

            $validated['slug'] = Str::slug($validated['judul']);

            // Handle image upload
            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($berita->gambar) {
                    Storage::disk('public')->delete($berita->gambar);
                }
                $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
            }

            if ($validated['is_published'] && !$berita->is_published && !isset($validated['published_at'])) {
                $validated['published_at'] = now();
            }

            $berita->update($validated);
            return response()->json($berita);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating berita: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Error updating berita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        
        // Delete image
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
        
        $berita->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

