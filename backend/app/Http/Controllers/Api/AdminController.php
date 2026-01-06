<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ========== DINAS MANAGEMENT ==========
    
    public function indexDinas(Request $request)
    {
        $query = Dinas::with('users');

        if ($request->has('search')) {
            $query->where('nama_dinas', 'like', '%' . $request->search . '%');
        }

        $dinas = $query->orderBy('nama_dinas', 'asc')->paginate(10);

        return response()->json($dinas);
    }

    public function storeDinas(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_dinas' => 'required|string|max:255',
                'kode_dinas' => 'nullable|string|max:50|unique:dinas',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            // Convert is_active to boolean
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['is_active'] = true;
            }

            $dinas = Dinas::create($validated);

            return response()->json($dinas->load('users'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing dinas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan dinas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showDinas($id)
    {
        $dinas = Dinas::with('users')->findOrFail($id);
        return response()->json($dinas);
    }

    public function updateDinas(Request $request, $id)
    {
        try {
            $dinas = Dinas::findOrFail($id);

            $validated = $request->validate([
                'nama_dinas' => 'required|string|max:255',
                'kode_dinas' => ['nullable', 'string', 'max:50', Rule::unique('dinas')->ignore($id)],
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            // Convert is_active to boolean
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            }

            $dinas->update($validated);

            return response()->json($dinas->load('users'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate dinas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyDinas($id)
    {
        $dinas = Dinas::findOrFail($id);
        $dinas->delete();

        return response()->json(['message' => 'Dinas deleted successfully']);
    }

    // ========== USER MANAGEMENT ==========

    public function indexUsers(Request $request)
    {
        $query = User::with('dinas');

        if ($request->has('dinas_id')) {
            $query->where('dinas_id', $request->dinas_id);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(10);

        return response()->json($users);
    }

    public function storeUser(Request $request)
    {
        try {
            // Convert empty string to null for dinas_id before validation
            $data = $request->all();
            if (isset($data['dinas_id']) && $data['dinas_id'] === '') {
                $data['dinas_id'] = null;
            }
            $request->merge($data);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:dinas,bagian_hukum,admin',
                'dinas_id' => 'nullable|exists:dinas,id',
                'unit_kerja' => 'nullable|string|max:255',
            ]);

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return response()->json($user->load('dinas'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing user: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showUser($id)
    {
        $user = User::with('dinas')->findOrFail($id);
        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Convert empty string to null for dinas_id before validation
            $data = $request->all();
            if (isset($data['dinas_id']) && $data['dinas_id'] === '') {
                $data['dinas_id'] = null;
            }
            $request->merge($data);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
                'password' => 'nullable|string|min:6',
                'role' => 'required|in:dinas,bagian_hukum,admin',
                'dinas_id' => 'nullable|exists:dinas,id',
                'unit_kerja' => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            // Convert is_active to boolean
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return response()->json($user->load('dinas'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleUserStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = !$user->is_active;
            $user->save();

            // Also update dinas status if user has dinas
            if ($user->dinas_id) {
                $dinas = Dinas::find($user->dinas_id);
                if ($dinas) {
                    // Update dinas status based on active users
                    $activeUsers = User::where('dinas_id', $dinas->id)
                        ->where('is_active', true)
                        ->count();
                    $dinas->is_active = $activeUsers > 0;
                    $dinas->save();
                }
            }

            return response()->json([
                'message' => $user->is_active ? 'User berhasil diaktifkan' : 'User berhasil dinonaktifkan',
                'user' => $user->load('dinas')
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengubah status user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    // Public endpoint untuk mendapatkan daftar dinas (untuk form registrasi)
    public function publicDinas()
    {
        $dinas = Dinas::where('is_active', true)
            ->orderBy('nama_dinas', 'asc')
            ->get(['id', 'nama_dinas', 'kode_dinas']);

        return response()->json($dinas);
    }
}

