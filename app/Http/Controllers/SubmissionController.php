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
    // 1. Validasi dengan pesan error custom agar jelas mana yang salah
    $request->validate([
        // Validasi nomor_spp_spm harus angka dan tepat 5 karakter
        'nomor_spp_spm'      => 'required|digits:5', 
        'jenis_pencairan'    => 'required|in:LS,GU,TU,LS Nihil,GU Nihil,TU Nihil',
        'tanggal'            => 'required|date',
        'judul_kegiatan'     => 'required|string|max:255',
        'judul_sub_kegiatan' => 'required|string|max:255',
        'perihal'            => 'required|string',
        'total_pencairan'    => 'required|numeric',
        'files'              => 'required|array|min:6',
        'files.*'            => 'required|mimes:pdf|max:10240',
    ], [
        // Pesan error kustom bahasa Indonesia
        'nomor_spp_spm.digits' => 'Nomor SPP harus berupa 5 digit angka (contoh: 00001).',
        'nomor_spp_spm.required' => 'Nomor SPP wajib diisi.',
        'files.*.required' => 'Dokumen ini wajib diunggah.',
        'files.*.mimes' => 'Format dokumen harus PDF.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
    ]);

    $user = auth()->user();

    // Cek apakah folder storage sudah siap
    if (!Storage::exists('public')) {
        Storage::makeDirectory('public');
    }

    DB::beginTransaction();

    try {
        $submission = Submission::create([
            'user_id'            => $user->id,
            'department_id'      => $user->department_id, 
            'nomor_spp_spm'      => $request->nomor_spp_spm,
            'jenis_pencairan'    => $request->jenis_pencairan,
            'tanggal'            => $request->tanggal,
            'judul_kegiatan'     => $request->judul_kegiatan,
            'judul_sub_kegiatan' => $request->judul_sub_kegiatan,
            'perihal'            => $request->perihal,
            'total_pencairan'    => $request->total_pencairan,
            'status'             => 'pending',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $jenis => $file) {
                // Pastikan file valid sebelum diproses
                if ($file->isValid()) {
                    $safeNomorSpm = Str::slug($request->nomor_spp_spm);
                    $fileName = strtoupper(Str::slug($jenis)) . '_' . $safeNomorSpm . '_' . time() . '.' . $file->getClientOriginalExtension();
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
        }

        DB::commit();
        return redirect()->route('submissions.index')->with('success', 'Berkas berhasil dikirim!');

    } catch (\Exception $e) {
        DB::rollback();
        // Log error ke storage/logs/laravel.log untuk kita intip penyebabnya
        \Log::error("Gagal Simpan iDJ: " . $e->getMessage());
        return back()->withInput()->with('error', 'Gagal memproses berkas: ' . $e->getMessage());
    }
}

    public function index()
    {
        $user = auth()->user();

        // Allowed sort columns
        $allowedSorts = ['tanggal', 'nomor_spp_spm', 'jenis_pencairan', 'judul_kegiatan', 'judul_sub_kegiatan', 'perihal', 'total_pencairan', 'status', 'created_at', 'departments.nama_dinas'];

        $sortBy = request('sort', 'created_at');
        $direction = request('direction', 'desc');

        // Validate sort column
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        // Validate direction
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = Submission::with('department', 'user');

        if (!$user->hasRole('super_admin')) {
            $query->where('department_id', $user->department_id);
        }

        // Apply filters
        $query->when(request('tanggal'), function($q) {
            $q->whereDate('tanggal', request('tanggal'));
        });

        $query->when(request('jenis_pencairan'), function($q) {
            $q->where('jenis_pencairan', request('jenis_pencairan'));
        });

        $query->when(request('nomor_spp_spm'), function($q) {
            $q->where('nomor_spp_spm', 'like', '%' . request('nomor_spp_spm') . '%');
        });

        if ($sortBy == 'departments.nama_dinas') {
            $submissions = $query->leftJoin('departments', 'submissions.department_id', '=', 'departments.id')
                                 ->orderBy('departments.nama_dinas', $direction)
                                 ->select('submissions.*')
                                 ->get();
        } else {
            $submissions = $query->orderBy($sortBy, $direction)->get();
        }

        return view('submissions.index', compact('submissions', 'sortBy', 'direction'));
    }

 public function show($id)
{
    // Eager load documents untuk efisiensi
    $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
    $user = auth()->user();

    // Proteksi Akses Operator
    if ($user->hasRole('operator')) {
        if ($submission->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat berkas dinas lain.');
        }
    }

    // LOGIKA TAMBAHAN: Cek apakah semua 6 berkas sudah disetujui (status 'approved')
    // Ini digunakan untuk mengaktifkan/menonaktifkan tombol "Kirim ke Kabid" di View
    $approvedCount = $submission->documents->where('status_dokumen', 'approved')->count();
    $isAllVerified = ($approvedCount === 6);

    // Kirim variabel isAllVerified ke view
    return view('submissions.show', compact('submission', 'isAllVerified'));
}

    public function editRevisi($id)
    {
        // Hanya mengambil dokumen yang statusnya 'rejected'
        $submission = Submission::with(['documents' => function($q) {
            $q->where('status_dokumen', 'rejected'); 
        }])->where('department_id', auth()->user()->department_id)->findOrFail($id);

        return view('submissions.edit', compact('submission'));
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

public function update(Request $request, $id)
{
    $submission = Submission::findOrFail($id);
    $user = auth()->user();

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $docId => $file) {
            $document = Document::findOrFail($docId);
            
            // Buat nama file konsisten dengan fungsi store
            $safeNomorSpm = Str::slug($submission->nomor_spp_spm);
            $fileName = strtoupper($document->jenis_dokumen) . '_' . $safeNomorSpm . '_' . time() . '.' . $file->getClientOriginalExtension();
            $folderPath = 'arsip/' . date('Y/m') . '/' . Str::slug($user->department->nama_dinas);

            // Simpan file
            $path = $file->storeAs($folderPath, $fileName, 'public');
            
            // Update data dokumen
            $document->update([
                'file_path' => $path,
                'status_dokumen' => 'direvisi', // Kembali ke pending untuk dicek ulang
                'catatan' => null
            ]);
        }
    }

    // Kembalikan status induk agar muncul kembali di dashboard Verifikator
    $submission->update([
        'status' => 'direvisi',
        'verified_by_name' => null, // Reset jejak verifikasi jika perlu
        'verified_at' => null
    ]);

    return back()->with('success', 'Berkas berhasil diperbaiki dan sedang dikirim ulang ke verifikator.');
}

public function updateStatus(Request $request, $id)
{
    $submission = Submission::findOrFail($id);
    
    // 1. Update status utama
    $submission->status = $request->status;
    $submission->save();

    // 2. Simpan jejak audit ke tabel log
    $submission->logs()->create([
        'user_id' => auth()->id(),
        'status_to' => $request->status,
        'message' => $request->catatan ?? 'Perubahan status sistem otomatis.',
    ]);

    return back()->with('success', 'Status berhasil diperbarui!');
}

}