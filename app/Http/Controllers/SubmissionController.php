<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Submission;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function create()
    {
        $userDepartment = auth()->user()->department;

        if (!$userDepartment) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum terhubung ke instansi manapun. Hubungi Admin.');
        }

        return view('submissions.create', compact('userDepartment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_spp_spm'      => 'required|string',
            'tanggal'            => 'required|date',
            'judul_kegiatan'     => 'required|string',
            'total_pencairan'    => 'required|numeric',
            'files'              => 'required|array',
            'files.*'            => 'required|mimes:pdf|max:10000',
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        try {
            // 1. Simpan Data Utama - Status Awal: pending
            $submission = Submission::create([
                'user_id'            => $user->id,
                'department_id'      => $user->department_id, 
                'nomor_spp_spm'      => $request->nomor_spp_spm,
                'tanggal'            => $request->tanggal,
                'judul_kegiatan'     => $request->judul_kegiatan,
                'judul_sub_kegiatan' => $request->judul_sub_kegiatan,
                'perihal'            => $request->perihal,
                'total_pencairan'    => $request->total_pencairan,
                'status'             => 'pending', // Operator lihat: Menunggu Verifikasi
            ]);

            // 2. Simpan File
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $jenis => $file) {
                    
                    $safeNomorSpm = Str::slug($request->nomor_spp_spm);
                    $fileName = strtoupper($jenis) . '_' . $safeNomorSpm . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    $folderPath = 'arsip/' . date('Y/m') . '/' . Str::slug($user->department->nama_dinas);

                    $path = $file->storeAs($folderPath, $fileName, 'public');
                    
                    Document::create([
                        'submission_id'  => $submission->id,
                        'jenis_dokumen'  => $jenis, 
                        'file_path'      => $path,
                        'status_dokumen' => 'pending',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('submissions.index')->with('success', 'Berkas berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $submissions = Submission::with('department', 'user')->latest()->get();
        } else {
            $submissions = Submission::where('department_id', $user->department_id)
                                ->with('user')
                                ->latest()
                                ->get();
        }

        return view('submissions.index', compact('submissions'));
    }

    public function show($id)
    {
        $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('operator')) {
            if ($submission->department_id !== $user->department_id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat berkas dinas lain.');
            }
        }

        return view('submissions.show', compact('submission'));
    }

    public function editRevisi($id)
    {
        // Hanya mengambil dokumen yang statusnya 'rejected'
        $submission = Submission::with(['documents' => function($q) {
            $q->where('status_dokumen', 'rejected'); 
        }])->where('department_id', auth()->user()->department_id)->findOrFail($id);

        return view('submissions.edit', compact('submission'));
    }

    public function updateRevisi(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $docId => $file) {
                $document = Document::findOrFail($docId);
                
                // Penamaan file revisi agar tetap rapi
                $safeNomorSpm = Str::slug($submission->nomor_spp_spm);
                $fileName = 'REVISI_' . strtoupper($document->jenis_dokumen) . '_' . $safeNomorSpm . '_' . time() . '.' . $file->getClientOriginalExtension();
                $folderPath = 'arsip/' . date('Y/m') . '/' . Str::slug(auth()->user()->department->nama_dinas);
                
                $path = $file->storeAs($folderPath, $fileName, 'public');

                $document->update([
                    'file_path' => $path,
                    'status_dokumen' => 'pending', // Balikkan status dokumen individu ke pending
                    'catatan' => null
                ]);
            }

            // KUNCI: Ubah status induk menjadi 'revisi'
            // Kasie akan melihat badge "Revisi", Operator melihat "Diperbaiki"
            $submission->update(['status' => 'revisi']);

            return redirect()->route('submissions.index')->with('success', 'Berkas perbaikan berhasil dikirim.');
        }

        return back()->with('error', 'Silakan pilih file yang akan diperbaiki.');
    }

// app/Http/Controllers/SubmissionController.php

public function allSubmissions(Request $request)
{
    $query = Submission::with('department');

    // 1. FILTER TAHUN ANGGARAN (Sebagai scope dasar)
    // Selalu batasi berdasarkan tahun anggaran yang dipilih atau tahun berjalan
    $tahun = $request->get('tahun', date('Y'));
    $query->whereYear('tanggal', $tahun);

    // 2. FILTER AKUMULATIF (Hanya aktif jika input diisi)
    // Menggunakan where() berantai sehingga fungsinya menjadi "AND"
    
    if ($request->filled('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    }

    if ($request->filled('nomor')) {
        // Cari yang mengandung nomor tersebut (LIKE)
        $query->where('nomor_spp_spm', 'like', '%' . $request->nomor . '%');
    }

    if ($request->filled('dinas')) {
        $query->where('department_id', $request->dinas);
    }

    if ($request->filled('min_nominal')) {
        // Jika ingin nominal yang PERSIS sama, gunakan '='
        // Jika ingin minimal, gunakan '>='
        $query->where('total_pencairan', '=', $request->min_nominal);
    }

    // 3. LOGIKA SORTING (Pengurutan)
    $sortField = $request->get('sort'); 
    $order = $request->get('order', 'desc'); 

    if ($sortField == 'nominal') {
        $query->orderBy('total_pencairan', $order);
    } elseif ($sortField == 'dinas') {
        $query->join('departments', 'submissions.department_id', '=', 'departments.id')
              ->select('submissions.*') // Penting agar ID submission tidak tertukar ID dinas
              ->orderBy('departments.nama_dinas', $order);
    } else {
        // Default: Berkas terbaru di atas
        $query->orderBy('created_at', 'desc');
    }

    // 4. EKSEKUSI
    $submissions = $query->get();
    $departments = Department::all();

    return view('bpk.monitoring', compact('submissions', 'departments'));
}

public function resetStatus($id)
{
    $submission = Submission::findOrFail($id);
    
    // Reset semua kolom jejak digital ke NULL dan status ke pending
    $submission->update([
        'status' => 'pending',
        'verified_by_name' => null,
        'verified_at' => null,
        'validated_by_name' => null,
        'validated_at' => null,
    ]);

    return redirect()->back()->with('warning', 'Status berkas berhasil direset ke Pending.');
}

public function verifier()
{
    // Asumsi nama kolom di tabel submissions adalah verified_by
    return $this->belongsTo(User::class, 'verified_by');
}
}