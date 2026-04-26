<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\User;
use App\Models\Department;

class SuperAdminController extends Controller
{
    // Dashboard: views/admin/dashboard.blade.php
    public function dashboard()
{
    // Mengambil data langsung tanpa perantara variabel yang kompleks
    $total_berkas = \App\Models\Submission::count();
    $total_pending = \App\Models\Submission::where('status', 'pending')->count();
    $total_user = \App\Models\User::count();
    $total_dinas = \App\Models\Department::count();

    return view('admin.dashboard', [
        'stats' => [
            'total_berkas' => $total_berkas,
            'pending'      => $total_pending,
            'user_total'   => $total_user,
            'dinas'        => $total_dinas,
        ]
    ]);
}

    public function index(Request $request)
    {
        // 1. Ambil semua data dinas untuk isi dropdown filter
        $departments = Department::all(); 

        // 2. Query dasar untuk berkas
        $query = Submission::with(['department', 'user']);

        // 3. Logika Filter
        if ($request->filled('dinas')) {
            $query->where('department_id', $request->dinas);
        }

        if ($request->filled('nomor')) {
            $query->where('nomor_spp_spm', 'like', '%' . $request->nomor . '%');
        }

        if ($request->filled('kegiatan')) {
            $query->where('judul_kegiatan', 'like', '%' . $request->kegiatan . '%');
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('min_nominal')) {
            $query->where('total_pencairan', '>=', $request->min_nominal);
        }

        $submissions = $query->latest()->paginate(10);

        // 4. Kirim variabel ke view
        return view('admin.monitoring', compact('submissions', 'departments'));
    }

  public function show($id)
{
    // Ambil submission beserta relasi documents-nya
    $submission = \App\Models\Submission::with(['user', 'department', 'documents'])->findOrFail($id);

    return view('admin.show', compact('submission'));
}

// Tambahkan juga fungsi reset agar tombolnya jalan
// app/Http/Controllers/SuperAdminController.php

public function resetStatus($id)
{
    // Gunakan DB Transaction agar jika salah satu gagal, semua dibatalkan
    return \DB::transaction(function () use ($id) {
        $submission = Submission::findOrFail($id);

        // 1. Reset data induk (Submission)
        $submission->update([
            'status'             => 'reset', // Menggunakan status reset yang baru kita buat
            'verified_by_name'   => null,
            'verified_at'        => null,
            'validated_by_name'  => null,
            'validated_at'       => null,
            'catatan'            => 'Status telah direset oleh Admin pada ' . now()->format('d/m/Y H:i'),
        ]);

        // 2. Reset semua status dokumen terkait menjadi pending kembali
        // Ini penting agar operator bisa mengunggah ulang jika perlu
        $submission->documents()->update([
            'status_dokumen' => 'pending',
            'catatan'        => 'Direset oleh Admin'
        ]);

        return redirect()->back()->with('success', 'Berkas berhasil direset ke titik awal.');
    });
}
}