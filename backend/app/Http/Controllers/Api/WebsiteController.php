<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteController extends Controller
{
    // Public endpoint untuk frontend website
    public function public()
    {
        $website = Website::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get()
            ->keyBy('key');

        return response()->json($website);
    }

    // Admin endpoints
    public function index()
    {
        $website = Website::orderBy('order', 'asc')->get();
        return response()->json($website);
    }

    public function store(Request $request)
    {
        // Convert is_active to boolean
        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
            ]);
        }
        
        $validated = $request->validate([
            'key' => 'required|string|unique:website,key',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:text,html,image',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
            'image_url' => 'nullable|url',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
            'bupati_photo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('website', 'public');
        }

        // Handle bupati photo upload
        if ($request->hasFile('bupati_photo')) {
            $validated['bupati_photo'] = $request->file('bupati_photo')->store('website/bupati', 'public');
        }

        // Handle wakil bupati photo upload
        if ($request->hasFile('wakil_bupati_photo')) {
            $validated['wakil_bupati_photo'] = $request->file('wakil_bupati_photo')->store('website/bupati', 'public');
        }

        // Handle sekda photo upload
        if ($request->hasFile('sekda_photo')) {
            $validated['sekda_photo'] = $request->file('sekda_photo')->store('website/bupati', 'public');
        }

        $website = Website::create($validated);
        return response()->json($website, 201);
    }

    public function show($id)
    {
        $website = Website::findOrFail($id);
        return response()->json($website);
    }

    public function update(Request $request, $id)
    {
        $website = Website::findOrFail($id);

        // Convert is_active to boolean
        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
            ]);
        }

        // Validasi tanpa foto dulu (karena foto optional dan bisa tidak dikirim)
        $validated = $request->validate([
            'key' => 'sometimes|required|string|unique:website,key,' . $id,
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => 'sometimes|required|in:text,html,image',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
            'image_url' => 'nullable|url',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
        ]);

        // Validasi foto secara terpisah jika ada
        // Catatan: Validasi dilakukan di sini untuk memastikan file valid sebelum diproses
        if ($request->hasFile('bupati_photo')) {
            try {
                $request->validate([
                    'bupati_photo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
                ]);
                \Log::info('Bupati photo validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Bupati photo validation failed', ['errors' => $e->errors()]);
                throw $e;
            }
        }
        if ($request->hasFile('wakil_bupati_photo')) {
            try {
                $request->validate([
                    'wakil_bupati_photo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
                ]);
                \Log::info('Wakil bupati photo validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Wakil bupati photo validation failed', ['errors' => $e->errors()]);
                throw $e;
            }
        }
        if ($request->hasFile('sekda_photo')) {
            try {
                $request->validate([
                    'sekda_photo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
                ]);
                \Log::info('Sekda photo validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Sekda photo validation failed', ['errors' => $e->errors()]);
                throw $e;
            }
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($website->file_path) {
                Storage::disk('public')->delete($website->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('website', 'public');
        }

        // Debug: Log semua request data untuk troubleshooting
        \Log::info('Update request data', [
            'has_bupati_file' => $request->hasFile('bupati_photo'),
            'has_wakil_file' => $request->hasFile('wakil_bupati_photo'),
            'has_sekda_file' => $request->hasFile('sekda_photo'),
            'all_keys' => array_keys($request->all()),
            'files' => array_keys($request->allFiles())
        ]);

        // Handle bupati photo upload or deletion
        if ($request->hasFile('bupati_photo')) {
            \Log::info('Bupati photo file received', [
                'file_name' => $request->file('bupati_photo')->getClientOriginalName(),
                'file_size' => $request->file('bupati_photo')->getSize(),
                'mime_type' => $request->file('bupati_photo')->getMimeType(),
                'old_photo' => $website->bupati_photo
            ]);
            
            // Delete old photo
            if ($website->bupati_photo) {
                Storage::disk('public')->delete($website->bupati_photo);
                \Log::info('Old bupati photo deleted', ['path' => $website->bupati_photo]);
            }
            
            $validated['bupati_photo'] = $request->file('bupati_photo')->store('website/bupati', 'public');
            \Log::info('New bupati photo stored', ['path' => $validated['bupati_photo']]);
        } elseif ($request->has('bupati_photo') && ($request->input('bupati_photo') === '' || $request->input('bupati_photo') === 'DELETE')) {
            // Hapus foto jika dikirim empty string atau "DELETE"
            if ($website->bupati_photo) {
                Storage::disk('public')->delete($website->bupati_photo);
                \Log::info('Bupati photo deleted by user request', ['path' => $website->bupati_photo]);
            }
            $validated['bupati_photo'] = null;
        } elseif ($website->bupati_photo) {
            // Hanya tambahkan ke validated jika foto sudah ada (jangan set null)
            $validated['bupati_photo'] = $website->bupati_photo;
            \Log::info('Bupati photo kept (no new file)', ['path' => $website->bupati_photo]);
        } else {
            \Log::info('No bupati photo to update or keep');
        }

        // Handle wakil bupati photo upload or deletion
        if ($request->hasFile('wakil_bupati_photo')) {
            // Delete old photo
            if ($website->wakil_bupati_photo) {
                Storage::disk('public')->delete($website->wakil_bupati_photo);
            }
            $validated['wakil_bupati_photo'] = $request->file('wakil_bupati_photo')->store('website/bupati', 'public');
        } elseif ($request->has('wakil_bupati_photo') && ($request->input('wakil_bupati_photo') === '' || $request->input('wakil_bupati_photo') === 'DELETE')) {
            // Hapus foto jika dikirim empty string atau "DELETE"
            if ($website->wakil_bupati_photo) {
                Storage::disk('public')->delete($website->wakil_bupati_photo);
            }
            $validated['wakil_bupati_photo'] = null;
        } elseif ($website->wakil_bupati_photo) {
            // Hanya tambahkan ke validated jika foto sudah ada (jangan set null)
            $validated['wakil_bupati_photo'] = $website->wakil_bupati_photo;
        }

        // Handle sekda photo upload or deletion
        if ($request->hasFile('sekda_photo')) {
            // Delete old photo
            if ($website->sekda_photo) {
                Storage::disk('public')->delete($website->sekda_photo);
            }
            $validated['sekda_photo'] = $request->file('sekda_photo')->store('website/bupati', 'public');
        } elseif ($request->has('sekda_photo') && ($request->input('sekda_photo') === '' || $request->input('sekda_photo') === 'DELETE')) {
            // Hapus foto jika dikirim empty string atau "DELETE"
            if ($website->sekda_photo) {
                Storage::disk('public')->delete($website->sekda_photo);
            }
            $validated['sekda_photo'] = null;
        } elseif ($website->sekda_photo) {
            // Hanya tambahkan ke validated jika foto sudah ada (jangan set null)
            $validated['sekda_photo'] = $website->sekda_photo;
        }

        // Log untuk debugging
        \Log::info('Updating website photos', [
            'id' => $id,
            'bupati_photo' => $validated['bupati_photo'] ?? null,
            'wakil_bupati_photo' => $validated['wakil_bupati_photo'] ?? null,
            'sekda_photo' => $validated['sekda_photo'] ?? null,
        ]);

        try {
            $website->update($validated);
            
            // Refresh model untuk mendapatkan data terbaru
            $website->refresh();
            
            \Log::info('Website updated successfully', [
                'id' => $website->id,
                'bupati_photo' => $website->bupati_photo,
                'wakil_bupati_photo' => $website->wakil_bupati_photo,
                'sekda_photo' => $website->sekda_photo,
            ]);

            return response()->json($website);
        } catch (\Exception $e) {
            \Log::error('Error updating website', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'validated_data' => $validated
            ]);
            return response()->json([
                'message' => 'Gagal menyimpan foto kepemimpinan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $website = Website::findOrFail($id);
        
        // Delete file if exists
        if ($website->file_path) {
            Storage::disk('public')->delete($website->file_path);
        }
        
        $website->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

