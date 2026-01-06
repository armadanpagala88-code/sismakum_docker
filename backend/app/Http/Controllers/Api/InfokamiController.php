<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Infokami;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InfokamiController extends Controller
{
    // Public endpoint
    public function public()
    {
        $infokami = Infokami::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($infokami);
    }

    // Admin endpoints
    public function index()
    {
        $infokami = Infokami::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $infokami
        ]);
    }
    
    // Alias for getAll (used by frontend)
    public function getAll()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        try {
            // Check if files are present first
            if (!$request->hasFile('file_pdf')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File PDF wajib diisi'
                ], 422);
            }
            
            if (!$request->hasFile('cover_pdf')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cover image wajib diisi'
                ], 422);
            }
            
            // Validate all fields including files
            $validated = $request->validate([
                'nama_file' => 'required|string|max:255',
                'file_pdf' => 'required|file|mimes:pdf|max:51200', // Max 50MB
                'cover_pdf' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
                'deskripsi' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Handle PDF file upload
            if ($request->hasFile('file_pdf')) {
                $file = $request->file('file_pdf');
                // Check file type
                if ($file->getMimeType() !== 'application/pdf') {
                    return response()->json([
                        'success' => false,
                        'message' => 'File PDF tidak valid. Pastikan file berformat PDF.'
                    ], 422);
                }
                $validated['file_pdf'] = $file->store('infokami', 'public');
            }

            // Handle cover image upload
            if ($request->hasFile('cover_pdf')) {
                $cover = $request->file('cover_pdf');
                // Check file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!in_array($cover->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cover image tidak valid. Pastikan file berformat JPG, PNG, GIF, atau WEBP.'
                    ], 422);
                }
                $validated['cover_pdf'] = $cover->store('infokami', 'public');
            }

            $validated['status'] = $validated['status'] ?? 'active';

            $infokami = Infokami::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'INFOKAMI berhasil ditambahkan',
                'data' => $infokami
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                $errorMessages = array_merge($errorMessages, $messages);
            }
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $errorMessages),
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $infokami = Infokami::findOrFail($id);
        return response()->json($infokami);
    }

    public function update(Request $request, $id)
    {
        try {
            $infokami = Infokami::findOrFail($id);

            $validated = $request->validate([
                'nama_file' => 'sometimes|required|string|max:255',
                'file_pdf' => 'sometimes|file|mimes:pdf|max:51200', // Max 50MB
                'cover_pdf' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
                'deskripsi' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Handle PDF file upload
            if ($request->hasFile('file_pdf')) {
                // Delete old file
                if ($infokami->file_pdf) {
                    Storage::disk('public')->delete($infokami->file_pdf);
                }
                $validated['file_pdf'] = $request->file('file_pdf')->store('infokami', 'public');
            }

            // Handle cover image upload
            if ($request->hasFile('cover_pdf')) {
                // Delete old file
                if ($infokami->cover_pdf) {
                    Storage::disk('public')->delete($infokami->cover_pdf);
                }
                $validated['cover_pdf'] = $request->file('cover_pdf')->store('infokami', 'public');
            }

            $infokami->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'INFOKAMI berhasil diupdate',
                'data' => $infokami->fresh()
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
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $infokami = Infokami::findOrFail($id);
        
        // Delete files
        if ($infokami->file_pdf) {
            Storage::disk('public')->delete($infokami->file_pdf);
        }
        if ($infokami->cover_pdf) {
            Storage::disk('public')->delete($infokami->cover_pdf);
        }
        
        $infokami->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

