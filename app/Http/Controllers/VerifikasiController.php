<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index()
    {
        // Kasie memantau: 
        // 1. pending (Baru)
        // 2. revisi (Perbaikan dari Operator)
        // 3. rejected (Masih ditolak/menunggu operator perbaiki)
        $submissions = Submission::with('department', 'user')
                        ->whereIn('status', ['pending', 'revisi', 'rejected', 'direvisi'])
                        ->oldest()->get();
                        
        return view('verifikasi.index', compact('submissions'));
    }

 public function show($id)
{
    $submission = Submission::with(['department', 'documents'])->findOrFail($id);
    
    // Hitung apakah semua dokumen sudah verified
    $totalDocs = $submission->documents->count();
    $verifiedDocs = $submission->documents->where('status_dokumen', 'verified')->count();
    $isAllVerified = ($totalDocs > 0 && $verifiedDocs == $totalDocs);

    return view('verifikasi.show', compact('submission', 'isAllVerified'));
}

  public function documentAction(Request $request, $docId)
{
    $doc = \App\Models\Document::findOrFail($docId);
    $submission = $doc->submission;

    // 1. Update status dokumen individu
    if ($request->action == 'setuju') {
        $doc->update([
            'status_dokumen' => 'verified', 
            'catatan' => null
        ]);
    } elseif ($request->action == 'revisi') {
        $doc->update([
            'status_dokumen' => 'revisi', 
            'catatan' => $request->catatan
        ]);
    }

    // 2. Logika Penentuan Status Induk
    $revisiDocsCount = $submission->documents()->where('status_dokumen', 'revisi')->count();
    $submission->update([
        'status' => ($revisiDocsCount > 0) ? 'revisi' : 'pending'
    ]);

    // --- TAMBAHAN UNTUK JSON ---
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Status berkas berhasil diperbarui.',
            'status_dokumen' => $doc->status_dokumen,
            'submission_status' => $submission->status
        ]);
    }

    return back()->with('success', 'Status berkas berhasil diperbarui.');
}
   public function sendToKabid(Request $request, $id)
{
    $submission = Submission::with('documents')->findOrFail($id);
    
    // 1. Validasi Keamanan: Pastikan semua dokumen sudah verified (6/6)
    $totalDocs = $submission->documents()->count();
    $verifiedDocs = $submission->documents()->where('status_dokumen', 'verified')->count();

    if ($verifiedDocs < $totalDocs || $totalDocs == 0) {
        return back()->with('error', 'Gagal! Masih ada berkas yang belum diverifikasi atau berkas kosong.');
    }
    
    // 2. Update Status Induk & Jejak Digital
    $submission->update([
        'status' => 'proses validasi', // Status berubah, maka otomatis hilang dari index Kasie
        'verified_by' => auth()->user()->name,
        'verified_at' => now(),
        'catatan' => null // Hapus catatan jika ada
    ]);

    return redirect()->route('verifikasi.index')->with('success', 'Berkas berhasil diteruskan untuk Proses Validasi.');
}

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|min:5',
        ]);

        $submission = Submission::findOrFail($id);
        $submission->update([
            'status' => 'revisi',
            'catatan' => $request->catatan
        ]);
        
        return redirect()->route('verifikasi.index')->with('error', 'Seluruh pengajuan ditolak.');
    }

}