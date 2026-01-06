<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Email atau password salah'
                ], 401);
            }

            // Check if user is active
            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Akun Anda belum diaktifkan. Silakan hubungi administrator.'
                ], 403);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'nip' => 'nullable|string|max:50',
                'jabatan' => 'nullable|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'dinas_id' => 'required|exists:dinas,id',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:20',
            ]);

            // Get dinas
            $dinas = \App\Models\Dinas::findOrFail($validated['dinas_id']);

            // Create user with dinas role and inactive status
            $user = User::create([
                'name' => $validated['name'],
                'nip' => $validated['nip'] ?? null,
                'jabatan' => $validated['jabatan'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'dinas',
                'unit_kerja' => $dinas->nama_dinas,
                'dinas_id' => $dinas->id,
                'is_active' => false, // Akun nonaktif sampai diaktifkan admin
            ]);

            // Update dinas info if provided
            if (isset($validated['alamat']) || isset($validated['telepon'])) {
                $dinas->update([
                    'alamat' => $validated['alamat'] ?? $dinas->alamat,
                    'telepon' => $validated['telepon'] ?? $dinas->telepon,
                ]);
            }

            return response()->json([
                'message' => 'Pendaftaran berhasil! Akun Anda akan diaktifkan oleh administrator.',
                'user' => $user->load('dinas')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

