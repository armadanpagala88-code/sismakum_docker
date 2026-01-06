<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideshowController extends Controller
{
    // Public endpoint untuk frontend website
    public function public()
    {
        $slideshow = Slideshow::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json($slideshow);
    }

    // Admin endpoints
    public function index()
    {
        $slideshow = Slideshow::orderBy('order', 'asc')->get();
        return response()->json($slideshow);
    }

    public function store(Request $request)
    {
        // Convert is_active to boolean
        $request->merge([
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
        ]);
        
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'link' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $validated['image_path'] = $request->file('file')->store('slideshow', 'public');
        }

        $slideshow = Slideshow::create($validated);
        return response()->json($slideshow, 201);
    }

    public function show($id)
    {
        $slideshow = Slideshow::findOrFail($id);
        return response()->json($slideshow);
    }

    public function update(Request $request, $id)
    {
        $slideshow = Slideshow::findOrFail($id);

        // Convert is_active to boolean
        if ($request->has('is_active')) {
            $request->merge([
                'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
            ]);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'link' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($slideshow->image_path) {
                Storage::disk('public')->delete($slideshow->image_path);
            }
            $validated['image_path'] = $request->file('file')->store('slideshow', 'public');
        }

        $slideshow->update($validated);
        return response()->json($slideshow);
    }

    public function destroy($id)
    {
        $slideshow = Slideshow::findOrFail($id);
        
        // Delete file if exists
        if ($slideshow->image_path) {
            Storage::disk('public')->delete($slideshow->image_path);
        }
        
        $slideshow->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
