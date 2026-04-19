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
                        ->whereIn('status', ['pending', 'revisi', 'rejected'])
                        ->oldest()->get();
                        
        return view('verifikasi.index', compact('submissions'));
    }

    public function show($id)
    {
        $submission = Submission::with(['department', 'documents'])->findOrFail($id);
        return view('verifikasi.show', compact('submission'));
    }

    public function documentAction(Request $request, $docId)
    {
        $doc = \App\Models\Document::findOrFail($docId);
        $submission = $doc->submission;

        // 1. Update status dokumen individu
        if ($request->action == 'setuju') {
            $doc->update(['status_dokumen' => 'verified', 'catatan' => null]);
        } elseif ($request->action == 'tolak') {
            $doc->update(['status_dokumen' => 'rejected', 'catatan' => $request->catatan]);
        }

        // 2. Logika Penentuan Status Induk (Workflow iDJ)
        $totalDocs = $submission->documents()->count();
        $verifiedDocs = $submission->documents()->where('status_dokumen', 'verified')->count();
        $rejectedDocs = $submission->documents()->where('status_dokumen', 'rejected')->count();

        if ($rejectedDocs > 0) {
            // Jika ada yang ditolak, status jadi 'rejected' (Operator lihat: Menunggu Perbaikan)
            $submission->update(['status' => 'rejected']);
        } elseif ($verifiedDocs == $totalDocs) {
            // Jika semua sudah OK, status jadi 'validasi' (Bola di tangan Kabid Perbendaharaan)
            $submission->update(['status' => 'proses validasi']);
        } else {
            // Jika belum semua diperiksa, tetap di status awalnya (pending atau revisi)
            // agar tidak "loncat" statusnya saat Kasie baru mencicil pemeriksaan.
            if ($submission->status != 'revisi') {
                $submission->update(['status' => 'pending']);
            }
        }

        return back()->with('success', 'Status berkas diperbarui.');
    }

    // Fungsi setuju massal jika diperlukan
 public function setuju($id)
{
    $submission = Submission::findOrFail($id);
    
    // Update status dan isi jejak digital
    $submission->update([
        'status' => 'proses validasi',
        'verified_by_name' => auth()->user()->name, // Mengambil nama Kasie yang login
        'verified_at' => now(),                     // Mengambil waktu saat ini
    ]);

    return redirect()->route('verifikasi.index')->with('success', 'Berkas diteruskan ke Kabid Perbendaharaan untuk validasi.');
}

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|min:5',
        ]);

        $submission = Submission::findOrFail($id);
        $submission->update([
            'status' => 'rejected',
            'catatan' => $request->catatan
        ]);
        
        return redirect()->route('verifikasi.index')->with('error', 'Seluruh pengajuan ditolak.');
    }
}