<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengusulanPerbubController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\SlideshowController;
use App\Http\Controllers\Api\KategoriDokumenController;
use App\Http\Controllers\Api\InfokamiController;
use App\Http\Controllers\Api\BannerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Public routes
Route::get('/website', [WebsiteController::class, 'public']);
Route::get('/slideshow', [SlideshowController::class, 'public']);
Route::get('/berita', [BeritaController::class, 'public']);
Route::get('/berita/headline', [BeritaController::class, 'headline']);
Route::get('/berita/{slug}', [BeritaController::class, 'showPublic']);
Route::get('/banners', [BannerController::class, 'public']);
Route::get('/kategori-dokumen', [KategoriDokumenController::class, 'index']);
Route::get('/dinas', [AdminController::class, 'publicDinas']);
Route::get('/infokami', [InfokamiController::class, 'public']);

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/register', [AuthController::class, 'register']);

// Document download route - protected with auth
Route::middleware('auth:sanctum')->get('/dokumen/{path}', function ($path) {
    $filePath = urldecode($path);
    
    // Handle both dokumen_perbub and review_files paths
    $allowedPrefixes = ['dokumen_perbub/', 'review_files/'];
    $hasValidPrefix = false;
    
    foreach ($allowedPrefixes as $prefix) {
        if (strpos($filePath, $prefix) === 0) {
            $hasValidPrefix = true;
            break;
        }
    }
    
    // Default to dokumen_perbub if no valid prefix
    if (!$hasValidPrefix) {
        $filePath = 'dokumen_perbub/' . $filePath;
    }
    
    if (!Storage::disk('public')->exists($filePath)) {
        abort(404, 'File not found');
    }
    
    $file = Storage::disk('public')->get($filePath);
    $mimeType = Storage::disk('public')->mimeType($filePath);
    $fileName = basename($filePath);
    
    return response($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->where('path', '.*')->name('dokumen.download');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('pengusulan-perbub', PengusulanPerbubController::class);
    Route::post('/pengusulan-perbub/{id}/ajukan', [PengusulanPerbubController::class, 'ajukan']);
    Route::post('/pengusulan-perbub/{id}/review', [PengusulanPerbubController::class, 'review']);
    Route::put('/pengusulan-perbub/{id}/update-revisi', [PengusulanPerbubController::class, 'updateAfterRevisi']);
    Route::put('/pengusulan-perbub/{id}/update-status', [PengusulanPerbubController::class, 'updateStatus']);
    Route::get('/pengusulan-perbub/{id}/status-history', [PengusulanPerbubController::class, 'getStatusHistory']);

    // INFOKAMI routes (accessible by authenticated users)
    Route::get('/infokami/all', [InfokamiController::class, 'getAll']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Dinas management
        Route::get('/dinas', [AdminController::class, 'indexDinas']);
        Route::post('/dinas', [AdminController::class, 'storeDinas']);
        Route::get('/dinas/{id}', [AdminController::class, 'showDinas']);
        Route::put('/dinas/{id}', [AdminController::class, 'updateDinas']);
        Route::delete('/dinas/{id}', [AdminController::class, 'destroyDinas']);

        // User management
        Route::get('/users', [AdminController::class, 'indexUsers']);
        Route::post('/users', [AdminController::class, 'storeUser']);
        Route::get('/users/{id}', [AdminController::class, 'showUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser']);

        // Website management
        Route::apiResource('website', WebsiteController::class);
        
        // Slideshow management
        Route::apiResource('slideshow', SlideshowController::class);
        
        // Berita management
        Route::apiResource('berita', BeritaController::class);
        
        // Kategori Dokumen management
        Route::apiResource('kategori-dokumen', KategoriDokumenController::class);
        
        // INFOKAMI management
        Route::apiResource('infokami', InfokamiController::class);
        
        // Banner management
        Route::apiResource('banners', BannerController::class);
    });
});

