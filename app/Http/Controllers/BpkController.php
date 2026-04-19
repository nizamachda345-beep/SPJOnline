<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission; // Tetap pakai model yang sama, tapi akses terbatas
use App\Models\Department;

class BpkController extends Controller
{
    // Halaman Daftar Audit
    public function index(Request $request)
    {
        $query = Submission::with('department');

        // Filter Tahun Anggaran
        $tahun = $request->get('tahun', date('Y'));
        $query->whereYear('tanggal', $tahun);

        // Filter Akumulatif (Hanya muncul jika diisi)
        if ($request->filled('tanggal')) $query->whereDate('tanggal', $request->tanggal);
        if ($request->filled('nomor')) $query->where('nomor_spp_spm', 'like', '%' . $request->nomor . '%');
        if ($request->filled('dinas')) $query->where('department_id', $request->dinas);
        if ($request->filled('min_nominal')) $query->where('total_pencairan', '=', $request->min_nominal);

        // Di dalam BpkController@index

        // Ambil parameter dari URL
        $sortField = $request->get('sort'); 
        $order = $request->get('order', 'desc'); // Default urutan menurun (terbaru/terbesar)

        if ($sortField == 'nominal') {
            $query->orderBy('total_pencairan', $order);
        } 
        elseif ($sortField == 'dinas') {
            $query->join('departments', 'submissions.department_id', '=', 'departments.id')
                ->select('submissions.*')
                ->orderBy('departments.nama_dinas', $order);
        } 
        elseif ($sortField == 'nomor') {
            $query->orderBy('nomor_spp_spm', $order);
        } 
        elseif ($sortField == 'tanggal' || $sortField == 'created_at') {
            $query->orderBy('created_at', $order);
        } 
        elseif ($sortField == 'status') {
            $query->orderBy('status', $order);
        } 
        else {
            // Default: Data yang paling baru diinput sistem muncul paling atas
            $query->orderBy('created_at', 'desc');
        }

        $submissions = $query->get();
        $departments = Department::all();

        return view('bpk.monitoring', compact('submissions', 'departments'));
    }

    // Halaman Detail Audit
    public function show($id)
    {
        // BPK hanya boleh melihat data yang sudah tervalidasi/masuk tahap audit
        $submission = Submission::with(['department', 'documents'])->findOrFail($id);
        return view('bpk.show', compact('submission'));
    }

    public function dashboard()
    {
        // BPK bisa melihat seluruh data (Audit Digital)
        $query = Submission::query();

        $stats = [
            'total_berkas'    => (clone $query)->count(),
            'total_nilai'     => (clone $query)->sum('total_pencairan'), // Tambahan khusus BPK
            'pending'         => (clone $query)->where('status', 'pending')->count(),
            'proses_validasi' => (clone $query)->where('status', 'proses validasi')->count(),
            'disetujui'       => (clone $query)->where('status', 'disetujui')->count(),
            'ditolak'         => (clone $query)->where('status', 'revisi')->count(), // Revisi = Perlu Atensi
        ];

        $recentSubmissions = $query->with(['department', 'user'])->latest()->take(10)->get();

        return view('bpk.dashboard', compact('stats', 'recentSubmissions'));
    }
}