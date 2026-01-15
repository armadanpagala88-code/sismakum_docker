<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengusulanPerbub;
use App\Models\DokumenPerbub;
use App\Models\CatatanRevisi;
use App\Models\StatusHistory;
use App\Mail\PengusulanStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class PengusulanPerbubController extends Controller
{
    public function index(Request $request)
    {
        $query = PengusulanPerbub::with(['dinas', 'reviewer', 'dokumen', 'catatanRevisi.creator', 'kategoriDokumen', 'statusHistory.updatedBy']);

        if ($request->user()->role === 'dinas') {
            $query->where('dinas_id', $request->user()->id);
        } elseif ($request->user()->role === 'verifikator') {
            // Verifikator bisa melihat semua usulan yang diajukan
            $query->whereIn('status', ['diajukan', 'revisi', 'usulan', 'pengkajian_usulan', 'pengkajian_draf', 'harmonisasi_kemenkumham', 'fasilitasi_biro_hukum_provinsi']);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $pengusulan = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($pengusulan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'kategori_dokumen_id' => 'nullable|exists:kategori_dokumen,id',
            'judul_perbub' => 'required|string',
            'latar_belakang' => 'required|string',
            'maksud_dan_tujuan' => 'required|string',
            'ruang_lingkup' => 'required|string',
            'dokumen' => 'nullable|array',
            'dokumen.*' => 'file|mimes:pdf,doc,docx|max:10240',
        ]);

        $validated['dinas_id'] = $request->user()->id;
        $validated['status'] = 'draft';

        $pengusulan = PengusulanPerbub::create($validated);

        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $path = $file->store('dokumen_perbub', 'public');
                DokumenPerbub::create([
                    'pengusulan_id' => $pengusulan->id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe_dokumen' => 'draft',
                    'ukuran_file' => $file->getSize(),
                ]);
            }
        }

        return response()->json($pengusulan->load(['dinas', 'dokumen', 'kategoriDokumen']), 201);
    }

    public function show($id)
    {
        $pengusulan = PengusulanPerbub::with(['dinas', 'reviewer', 'dokumen', 'catatanRevisi.creator', 'kategoriDokumen', 'statusHistory.updatedBy'])->findOrFail($id);
        return response()->json($pengusulan);
    }

    public function update(Request $request, $id)
    {
        $pengusulan = PengusulanPerbub::findOrFail($id);

        if ($pengusulan->dinas_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nomor_surat' => 'sometimes|required|string',
            'tanggal_surat' => 'sometimes|required|date',
            'kategori_dokumen_id' => 'nullable|exists:kategori_dokumen,id',
            'judul_perbub' => 'sometimes|required|string',
            'latar_belakang' => 'sometimes|required|string',
            'maksud_dan_tujuan' => 'sometimes|required|string',
            'ruang_lingkup' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:draft,diajukan',
        ]);

        $pengusulan->update($validated);

        return response()->json($pengusulan->load(['dinas', 'dokumen', 'kategoriDokumen']));
    }

    public function ajukan(Request $request, $id)
    {
        $pengusulan = PengusulanPerbub::findOrFail($id);

        if ($pengusulan->dinas_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pengusulan->update(['status' => 'diajukan']);

        // Simpan history status
        StatusHistory::create([
            'pengusulan_id' => $pengusulan->id,
            'status' => 'diajukan',
            'catatan' => 'Usulan telah diajukan ke Bagian Hukum',
            'updated_by' => $request->user()->id,
        ]);

        // Send email notification
        try {
            Mail::to($pengusulan->dinas->email)->send(
                new PengusulanStatusNotification($pengusulan, 'Usulan Anda telah diajukan ke Bagian Hukum')
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }

        return response()->json($pengusulan->load(['dinas', 'dokumen', 'kategoriDokumen', 'statusHistory.updatedBy']));
    }

    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak,revisi',
            'catatan' => 'required|string',
            'file_review' => 'nullable|file|mimes:doc,docx|max:10240',
            'file_review_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $pengusulan = PengusulanPerbub::findOrFail($id);

        // Verifikator, bagian_hukum, dan admin bisa review
        if (!in_array($request->user()->role, ['verifikator', 'bagian_hukum', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update status pengusulan
        $pengusulan->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Simpan history status
        StatusHistory::create([
            'pengusulan_id' => $pengusulan->id,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'updated_by' => $request->user()->id,
        ]);

        // Handle Word file upload
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file_review')) {
            $file = $request->file('file_review');
            $filePath = $file->store('review_files', 'public');
            $fileName = $file->getClientOriginalName();
        }

        // Handle PDF file upload
        $filePdfPath = null;
        $filePdfName = null;
        if ($request->hasFile('file_review_pdf')) {
            $file = $request->file('file_review_pdf');
            $filePdfPath = $file->store('review_files', 'public');
            $filePdfName = $file->getClientOriginalName();
        }

        // Simpan catatan revisi dengan file
        $tipe = $request->status === 'revisi' ? 'revisi' : ($request->status === 'ditolak' ? 'tolak' : 'catatan');
        CatatanRevisi::create([
            'pengusulan_id' => $pengusulan->id,
            'created_by' => $request->user()->id,
            'tipe' => $tipe,
            'catatan' => $request->catatan,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_review_pdf' => $filePdfPath,
            'file_review_pdf_name' => $filePdfName,
        ]);

        // Send email notification to dinas
        try {
            $pengusulan->load('dinas');
            if ($pengusulan->dinas && $pengusulan->dinas->email) {
                Mail::to($pengusulan->dinas->email)->send(
                    new PengusulanStatusNotification($pengusulan, $request->catatan)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }

        return response()->json($pengusulan->load(['dinas', 'reviewer', 'dokumen', 'catatanRevisi.creator', 'kategoriDokumen', 'statusHistory.updatedBy']));
    }


    // Update status usulan (untuk admin/bagian_hukum)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:usulan,pengkajian_usulan,pengkajian_draf,harmonisasi_kemenkumham,fasilitasi_biro_hukum_provinsi,selesai,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $pengusulan = PengusulanPerbub::findOrFail($id);

        // Hanya admin, bagian_hukum, dan verifikator yang bisa update status
        if (!in_array($request->user()->role, ['admin', 'bagian_hukum', 'verifikator'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $oldStatus = $pengusulan->status;
        $pengusulan->update([
            'status' => $request->status,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Simpan history status
        StatusHistory::create([
            'pengusulan_id' => $pengusulan->id,
            'status' => $request->status,
            'catatan' => $request->catatan ?? "Status berubah dari {$oldStatus} ke {$request->status}",
            'updated_by' => $request->user()->id,
        ]);

        // Send email notification to dinas
        try {
            $pengusulan->load('dinas');
            if ($pengusulan->dinas && $pengusulan->dinas->email) {
                Mail::to($pengusulan->dinas->email)->send(
                    new PengusulanStatusNotification($pengusulan, $request->catatan ?? "Status usulan Anda telah diubah menjadi: " . $this->getStatusLabel($request->status))
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }

        return response()->json($pengusulan->load(['dinas', 'reviewer', 'dokumen', 'catatanRevisi.creator', 'kategoriDokumen', 'statusHistory.updatedBy']));
    }

    // Get status history untuk tracking
    public function getStatusHistory($id)
    {
        $pengusulan = PengusulanPerbub::findOrFail($id);
        
        // Dinas hanya bisa melihat history usulan mereka sendiri
        if (request()->user()->role === 'dinas' && $pengusulan->dinas_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $history = StatusHistory::where('pengusulan_id', $id)
            ->with('updatedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    // Helper function untuk label status
    private function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'usulan' => 'Usulan',
            'pengkajian_usulan' => 'Pengkajian Usulan',
            'pengkajian_draf' => 'Pengkajian Draf',
            'harmonisasi_kemenkumham' => 'Harmonisasi Kemenkumham',
            'fasilitasi_biro_hukum_provinsi' => 'Fasilitasi Biro Hukum Provinsi',
            'diterima' => 'Diterima',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'revisi' => 'Revisi',
        ];

        return $labels[$status] ?? $status;
    }

    // Update usulan setelah revisi
    public function updateAfterRevisi(Request $request, $id)
    {
        $pengusulan = PengusulanPerbub::findOrFail($id);

        // Hanya dinas yang membuat usulan bisa update setelah revisi
        if ($pengusulan->dinas_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hanya bisa update jika status revisi
        if ($pengusulan->status !== 'revisi') {
            return response()->json(['message' => 'Usulan tidak dalam status revisi'], 400);
        }

        $validated = $request->validate([
            'nomor_surat' => 'sometimes|required|string',
            'tanggal_surat' => 'sometimes|required|date',
            'kategori_dokumen_id' => 'nullable|exists:kategori_dokumen,id',
            'judul_perbub' => 'sometimes|required|string',
            'latar_belakang' => 'sometimes|required|string',
            'maksud_dan_tujuan' => 'sometimes|required|string',
            'ruang_lingkup' => 'sometimes|required|string',
            'dokumen' => 'nullable|array',
            'dokumen.*' => 'file|mimes:pdf,doc,docx|max:10240',
        ]);

        $pengusulan->update($validated);

        // Upload dokumen revisi jika ada
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $path = $file->store('dokumen_perbub', 'public');
                DokumenPerbub::create([
                    'pengusulan_id' => $pengusulan->id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe_dokumen' => 'revisi',
                    'ukuran_file' => $file->getSize(),
                ]);
            }
        }

        // Tandai catatan revisi sebagai resolved
        $pengusulan->catatanRevisi()
            ->where('is_resolved', false)
            ->where('tipe', 'revisi')
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]);

        // Ubah status kembali ke diajukan
        $pengusulan->update(['status' => 'diajukan']);

        // Simpan history status
        StatusHistory::create([
            'pengusulan_id' => $pengusulan->id,
            'status' => 'diajukan',
            'catatan' => 'Usulan telah direvisi dan diajukan kembali',
            'updated_by' => $request->user()->id,
        ]);

        return response()->json($pengusulan->load(['dinas', 'reviewer', 'dokumen', 'catatanRevisi.creator', 'kategoriDokumen', 'statusHistory.updatedBy']));
    }

    public function destroy($id)
    {
        $pengusulan = PengusulanPerbub::findOrFail($id);
        $pengusulan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function deleteCatatanRevisi(Request $request, $id)
    {
        $catatan = CatatanRevisi::findOrFail($id);
        
        // Only admin, bagian_hukum, and verifikator can delete catatan
        if (!in_array($request->user()->role, ['admin', 'bagian_hukum', 'verifikator'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete associated files if exist
        if ($catatan->file_path) {
            Storage::disk('public')->delete($catatan->file_path);
        }
        if ($catatan->file_review_pdf) {
            Storage::disk('public')->delete($catatan->file_review_pdf);
        }

        $catatan->delete();

        return response()->json(['message' => 'Catatan berhasil dihapus']);
    }
}

