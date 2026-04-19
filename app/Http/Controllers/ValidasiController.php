<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    public function index()
    {
        // Pastikan status yang dicari adalah 'proses validasi'
        // sesuai dengan yang diupdate oleh Kasie sebelumnya.
        $submissions = Submission::with(['department', 'user', 'documents'])
            ->where('status', 'proses validasi')
            ->oldest()
            ->get();

        return view('validasi.index', compact('submissions'));
    }

    public function show($id)
    {
        $submission = Submission::with(['department', 'documents', 'user'])->findOrFail($id);

        return view('validasi.show', compact('submission'));
    }

    public function sahkan($id)
    {
        $submission = Submission::findOrFail($id);

        // Update status dan isi jejak digital Kabid Perbendaharaan
        $submission->update([
            'status' => 'disetujui',
            'validated_by_name' => auth()->user()->name,
            'validated_at' => now(),
        ]);

        return redirect()->route('validasi.index')->with('success', 'Berkas Disahkan!');
    }

    public function tolak(Request $request, $id)
    {
        // Validasi catatan penolakan
        $request->validate([
            'catatan' => 'required|string|min:10',
        ], [
            'catatan.required' => 'Mohon berikan alasan penolakan agar operator dapat memperbaiki berkas.',
        ]);

        $submission = Submission::findOrFail($id);

        // 1. Update status induk ke 'rejected' (Menunggu Perbaikan)
        $submission->update([
            'status' => 'rejected',
            'catatan' => 'PENOLAKAN KABID PERBENDAHARAAN: ' . $request->catatan,
        ]);

        // 2. Otomatis setel semua dokumen di dalamnya menjadi 'rejected'
        // agar operator wajib memperbaiki atau mengunggah ulang dokumen.
        $submission->documents()->update([
            'status_dokumen' => 'rejected',
            'catatan' => 'Ditolak oleh Kabid Perbendaharaan',
        ]);

        return redirect()->route('validasi.index')->with('error', 'Seluruh berkas telah dikembalikan ke Operator untuk perbaikan total.');
    }
}
