<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Submission;
use App\Models\Department;
use App\Models\User;
use App\Services\SystemSettingsService;
use App\Notifications\SystemActivityNotification;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SubmissionController extends Controller
{
    /**
     * Mengambil nama disk aktif dari konfigurasi (default 's3' di production/Wasmer Edge).
     */
    private function storageDisk(): string
    {
        return config('filesystems.default', 's3');
    }

    // Method: documentMatchesType
    private function documentMatchesType(?string $documentType, string $expectedType): bool
    {
        return str_contains(
            strtoupper(trim((string) $documentType)),
            strtoupper(trim($expectedType))
        );
    }

    // Method: notifyDocumentUploaded
    private function notifyDocumentUploaded(Submission $submission, string $documentType): void
    {
        try {
            $recipients = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['kabid_perbendaharaan', 'kasie_bud', 'super_admin']);
            })->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new SystemActivityNotification(
                    $documentType . ' Diunggah',
                    $documentType . ' untuk SPM No. ' . $submission->nomor_spm . ' dari ' . ($submission->department->nama_dinas ?? 'Instansi') . ' telah diunggah dan menunggu verifikasi.',
                    'fa-file-pdf',
                    'info',
                    [
                        'submission_id' => $submission->id,
                        'nomor_spm' => $submission->nomor_spm,
                        'nomor_spp' => $submission->nomor_spp,
                        'jenis_pencairan' => $submission->jenis_pencairan,
                        'jenis_dokumen' => $documentType,
                        'nama_dinas' => $submission->department->nama_dinas ?? '-',
                    ]
                ));
            }
        } catch (\Throwable $exception) {
            Log::warning('Notifikasi dokumen gagal dikirim.', [
                'submission_id' => $submission->id,
                'jenis_dokumen' => $documentType,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    // Method: create
    public function create(Request $request): View|RedirectResponse
    {
        $user = auth()->guard()->user();
        $userDepartment = $user->department;

        if (!$userDepartment) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum terhubung ke instansi manapun. Hubungi Admin.');
        }

        if ($user->hasRole('operator')) {
            $accessContext = $request->attributes->get('department_submission_access');
            $now = CarbonImmutable::now(app(SystemSettingsService::class)->timezone());
            $sameRequestSnapshot = is_array($accessContext)
                && ($accessContext['department_id'] ?? null) === (int) $userDepartment->id
                && ($accessContext['manual_access'] ?? null) === $userDepartment->akses_pengajuan_manual
                && ($accessContext['evaluated_at'] ?? null) instanceof CarbonImmutable
                && $accessContext['evaluated_at']->format('Y-m-d H:i') === $now->format('Y-m-d H:i')
                && is_array($accessContext['decision'] ?? null)
                && array_key_exists('allowed', $accessContext['decision']);
            $availability = $sameRequestSnapshot
                ? $accessContext['decision']
                : app(SystemSettingsService::class)->submissionAvailability($userDepartment, $now);

            if (!$availability['allowed']) {
                return redirect()->route('dashboard')->with('error', $availability['message']);
            }
        }

        return view('submissions.create', compact('userDepartment'));
    }

    // Method: store
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->guard()->user();
        $user = $user->load('department');
        $expiresAt = Carbon::now()->addDays(2);
        $disk = $this->storageDisk();

        if ($user->hasRole('operator')) {
            $department = $user->department;
            if (!$department) {
                return redirect()->route('dashboard')->with('error', 'Akun Anda belum terhubung ke instansi manapun.');
            }

            $accessContext = $request->attributes->get('department_submission_access');
            $now = CarbonImmutable::now(app(SystemSettingsService::class)->timezone());
            $sameRequestSnapshot = is_array($accessContext)
                && ($accessContext['department_id'] ?? null) === (int) $department->id
                && ($accessContext['manual_access'] ?? null) === $department->akses_pengajuan_manual
                && ($accessContext['evaluated_at'] ?? null) instanceof CarbonImmutable
                && $accessContext['evaluated_at']->format('Y-m-d H:i') === $now->format('Y-m-d H:i')
                && is_array($accessContext['decision'] ?? null)
                && array_key_exists('allowed', $accessContext['decision']);
            $availability = $sameRequestSnapshot
                ? $accessContext['decision']
                : app(SystemSettingsService::class)->submissionAvailability($department, $now);

            if (!$availability['allowed']) {
                return redirect()->route('dashboard')->with('error', $availability['message']);
            }
        }

        $documentMaxSizeMb = (int) app(SystemSettingsService::class)->getTyped('submission.document_max_size_mb');
        $documentMaxSizeKb = $documentMaxSizeMb * 1024;

        $request->validate([
            'nomor_spp'              => 'required|digits:5',
            'tanggal_spp'            => 'required|date',
            'nomor_spm'              => 'required|digits:5',
            'tanggal_spm'            => 'required|date',
            'jenis_pencairan'        => 'required|in:LS,GU,TU,GU Nihil,TU Nihil,UP',
            'sumber_dana'            => 'required|in:DAU,DBH,PAD,DAK',
            'tanggal'                => 'required|date',
            'judul_kegiatan'         => 'required|string|max:255',
            'judul_sub_kegiatan'     => 'required|string|max:255',
            'perihal'                => 'required|string',
            'total_pencairan'        => 'required|numeric',
        ]);

        $jenisPencairan = $request->input('jenis_pencairan');

        $documentRules = [
            'files' => 'required|array',
        ];

        $documentMessages = [
            'files.required'   => 'Dokumen pengajuan wajib diunggah.',
            'files.array'      => 'Format dokumen pengajuan tidak valid.',
            'files.*.required' => 'Dokumen wajib diunggah.',
            'files.*.file'     => 'File dokumen tidak valid.',
            'files.*.mimes'    => 'Format dokumen harus PDF.',
            'files.*.max'      => 'Ukuran file tidak boleh lebih dari ' . $documentMaxSizeMb . ' MB.',
        ];

        switch ($jenisPencairan) {
            case 'LS':
                $documentRules['files.ls_dokumen'] = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentRules['files.spj']        = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentMessages['files.ls_dokumen.required'] = 'Dokumen LS (SPP, Verifikasi SPP/SPM, SPTJM, Billing) wajib diunggah.';
                $documentMessages['files.spj.required']        = 'Dokumen SPJ wajib diunggah.';
                break;

            case 'TU':
                $documentRules['files.tu_dokumen'] = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentMessages['files.tu_dokumen.required'] = 'Dokumen TU (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD) wajib diunggah.';
                break;

            case 'GU':
                $documentRules['files.gu_dokumen'] = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentRules['files.spj']        = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentMessages['files.gu_dokumen.required'] = 'Dokumen GU (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD) wajib diunggah.';
                $documentMessages['files.spj.required']        = 'Dokumen SPJ wajib diunggah.';
                break;

            case 'UP':
                $documentRules['files.up_dokumen'] = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentMessages['files.up_dokumen.required'] = 'Dokumen UP (SPP, Verifikasi SPP/SPM, SPTJM) wajib diunggah.';
                break;

            case 'TU Nihil':
                $documentRules['files.spj']     = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentRules['files.sts']     = 'nullable|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentRules['files.sp2d_tu'] = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentMessages['files.spj.required']     = 'Dokumen SPJ wajib diunggah.';
                $documentMessages['files.sp2d_tu.required'] = 'Dokumen SP2D pada pengajuan TU wajib diunggah.';
                break;

            case 'GU Nihil':
                $documentRules['files.spj'] = 'required|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentRules['files.sts'] = 'nullable|file|mimes:pdf|max:' . $documentMaxSizeKb;
                $documentMessages['files.spj.required'] = 'Dokumen SPJ wajib diunggah.';
                break;
        }

        $request->validate($documentRules, $documentMessages);

        // Hapus pengecekan direktori lokal 'public' karena menggunakan Object Storage (Backblaze B2)
        if ($disk === 'public' && !Storage::exists('public')) {
            Storage::makeDirectory('public');
        }

        DB::beginTransaction();

        try {
            $submission = Submission::query()->create([
                'user_id'            => $user->id,
                'department_id'      => $user->department_id,
                'nomor_spp'          => $request->input('nomor_spp'),
                'tanggal_spp'        => $request->input('tanggal_spp'),
                'nomor_spm'          => $request->input('nomor_spm'),
                'tanggal_spm'        => $request->input('tanggal_spm'),
                'jenis_pencairan'    => $request->input('jenis_pencairan'),
                'sumber_dana'        => $request->input('sumber_dana'),
                'tanggal'            => $request->input('tanggal'),
                'judul_kegiatan'     => $request->input('judul_kegiatan'),
                'judul_sub_kegiatan' => $request->input('judul_sub_kegiatan'),
                'perihal'            => $request->input('perihal'),
                'total_pencairan'    => $request->input('total_pencairan'),
                'expires_at'         => $expiresAt,
                'status'             => 'pending',
            ]);

            if ($request->hasFile('files')) {
                $labels = [
                    'ls_dokumen'       => 'Dokumen LS',
                    'spj'              => 'Dokumen SPJ',
                    'tu_dokumen'       => 'Dokumen TU',
                    'gu_dokumen'       => 'Dokumen GU',
                    'up_dokumen'       => 'Dokumen UP',
                    'tu_nihil_dokumen' => 'Dokumen TU Nihil',
                    'sts'              => 'Dokumen STS',
                    'sp2d_tu'          => 'SP2D',
                ];

                foreach ($request->file('files') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $namaJenisDokumen = $labels[$key]
                            ?? ucwords(str_replace('_', ' ', $key));

                        $safeNomorSpm = Str::slug($request->input('nomor_spm'));

                        $fileName = strtoupper(Str::slug($namaJenisDokumen))
                            . '_'
                            . $safeNomorSpm
                            . '_'
                            . time()
                            . '.'
                            . $file->getClientOriginalExtension();

                        $folderPath = 'arsip/'
                            . date('Y/m')
                            . '/'
                            . Str::slug($user->department->nama_dinas ?? 'skpd');

                        $path = $file->storeAs($folderPath, $fileName, $disk);

                        if (!$path) {
                            throw new \RuntimeException("Gagal mengunggah file {$namaJenisDokumen} ke storage.");
                        }

                        Document::query()->create([
                            'submission_id'  => $submission->id,
                            'jenis_dokumen'  => $namaJenisDokumen,
                            'file_path'      => $path,
                            'status_dokumen' => 'pending',
                        ]);
                    }
                }
            }

            $additionalData = [
                'submission_id'   => $submission->id,
                'nama_operator'   => $user->name,
                'nama_dinas'      => $user->department->nama_dinas ?? '-',
                'nomor_spm'       => $request->input('nomor_spm'),
                'nomor_spp'       => $request->input('nomor_spp'),
                'jenis_pencairan' => $request->input('jenis_pencairan'),
                'total_pencairan' => $request->input('total_pencairan'),
            ];

            $targetRoles = [
                'kabid_perbendaharaan',
                'kasie_bud',
                'super_admin'
            ];

            $recipients = User::whereHas('roles', function ($q) use ($targetRoles) {
                $q->whereIn('name', $targetRoles);
            })->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(
                    new SystemActivityNotification(
                        'Pengajuan Baru',
                        'SPM No. '
                        . $request->input('nomor_spm')
                        . ' dari '
                        . ($user->department->nama_dinas ?? 'Instansi')
                        . ' menunggu untuk diverifikasi.',
                        'fa-file-alt',
                        'primary',
                        $additionalData
                    )
                );
            }

            DB::commit();

            return redirect()
                ->route('submissions.index')
                ->with(
                    'success',
                    'Pengajuan berhasil dikirim dan berlaku selama 2 hari (hingga '
                    . $expiresAt->format('d-m-Y H:i')
                    . ').'
                );

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal Simpan iDJ: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Gagal memproses berkas: ' . $e->getMessage());
        }
    }

    // Method: index
    public function index(): View
    {
        $user = auth()->guard()->user();
        $isTerkunci = false;
        $submissionBlockMessage = null;

        Submission::query()
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->each(function ($submission) {
                $submission->update(['status' => 'expired']);
                $submission->documents()->update(['status_dokumen' => 'expired']);
            });

        if ($user && $user->hasRole('operator')) {
            $department = $user->department;
            $availability = $department
                ? app(SystemSettingsService::class)->submissionAvailability($department)
                : ['allowed' => false, 'message' => 'Akun Anda belum terhubung ke instansi manapun.'];
            $isTerkunci = !$availability['allowed'];
            $submissionBlockMessage = $availability['message'] ?? null;
        }

        $sortBy = request('sort', 'created_at');
        $direction = request('direction', 'desc');

        $allowedSorts = ['tanggal', 'nomor_spp', 'nomor_spm', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query = Submission::query()->with('department', 'user', 'documents');

        if (!$user->hasRole('super_admin')) {
            $query->where('department_id', $user->department_id);
        }

        $query->when(request('nomor'), function ($q) {
            $search = request('nomor');
            $q->where(function ($sq) use ($search) {
                $sq->where('nomor_spp', 'like', "%$search%")
                    ->orWhere('nomor_spm', 'like', "%$search%");
            });
        });

        $submissionStatus = request('status');
        $statusGroups = [
            'pending'           => ['pending'],
            'proses_verifikasi' => ['proses verifikasi'],
            'direvisi'          => ['direvisi'],
            'revisi'            => ['revisi'],
            'validasi'          => ['proses validasi'],
            'disetujui'         => ['disetujui', 'approved', 'verified'],
            'ditolak'           => ['ditolak', 'rejected'],
            'expired'           => ['expired'],
            'reset'             => ['reset'],
        ];

        if (is_string($submissionStatus) && isset($statusGroups[$submissionStatus])) {
            $query->whereIn('status', $statusGroups[$submissionStatus]);
        } elseif ($submissionStatus === 'sp2d_needs_upload') {
            $query->whereIn('jenis_pencairan', ['LS', 'GU', 'TU', 'UP'])
                ->whereIn('status', ['disetujui', 'approved'])
                ->whereDoesntHave('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%');
                });
        } elseif ($submissionStatus === 'sp2d_revision') {
            $query->whereIn('jenis_pencairan', ['LS', 'GU', 'TU', 'UP'])
                ->whereIn('status', ['disetujui', 'approved'])
                ->whereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->where('status_dokumen', 'revisi');
                });
        } elseif ($submissionStatus === 'sp2d_verification') {
            $query->whereIn('jenis_pencairan', ['LS', 'GU', 'TU', 'UP'])
                ->whereIn('status', ['disetujui', 'approved'])
                ->whereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->whereIn('status_dokumen', ['uploaded', 'direvisi']);
                });
        } elseif ($submissionStatus === 'sp2d_complete') {
            $query->where(function ($submission) {
                $submission->whereRaw(
                    "UPPER(TRIM(COALESCE(jenis_pencairan, ''))) = ?",
                    ['GU NIHIL']
                )->orWhereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->whereIn('status_dokumen', ['verified', 'approved']);
                });
            });
        }

        $submissions = $query->orderBy($sortBy, $direction)
                           ->paginate(10)
                           ->withQueryString();

        return view('submissions.index', compact('submissions', 'sortBy', 'direction', 'isTerkunci', 'submissionBlockMessage'));
    }

    // Method: sp2dUploadIndex
    public function sp2dUploadIndex(): View
    {
        $user = auth()->guard()->user();

        $submissions = Submission::query()
            ->with(['department', 'documents'])
            ->where('department_id', $user->department_id)
            ->whereIn('status', ['disetujui', 'approved'])
            ->whereRaw("UPPER(TRIM(COALESCE(jenis_pencairan, ''))) IN ('LS', 'GU', 'TU', 'UP')")
            ->where(function ($query) {
                $query->whereDoesntHave('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%');
                })->orWhereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->where('status_dokumen', 'revisi');
                });
            })
            ->latest('created_at')
            ->paginate(10);

        return view('submissions.sp2d-upload', compact('submissions'));
    }

    // Method: show
    public function show(int $id): View
    {
        $submission = Submission::query()->with(['department', 'user', 'documents'])->findOrFail($id);

        $user = auth()->guard()->user();

        if ($user->hasRole('operator') && $submission->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action. Anda tidak berhak mengakses data instansi lain.');
        }

        if ($submission->status === 'pending' && $submission->expires_at && Carbon::now()->greaterThan($submission->expires_at)) {
            $submission->update(['status' => 'expired']);
            $submission->documents()->update(['status_dokumen' => 'expired']);
            $submission->refresh();
        }

        $totalFiles = $submission->documents->count();
        $approvedCount = $submission->documents->where('status_dokumen', 'approved')->count();
        $isAllVerified = ($totalFiles > 0 && $approvedCount === $totalFiles);

        return view('submissions.show', compact('submission', 'isAllVerified'));
    }

    // Method: resetStatus
    public function resetStatus(int $id): RedirectResponse
    {
        $submission = Submission::query()->findOrFail($id);

        $user = auth()->guard()->user();

        if ($user->hasRole('operator') && $submission->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $submission->update([
            'status'            => 'pending',
            'verified_by_name'  => null,
            'verified_at'       => null,
            'validated_by_name' => null,
            'validated_at'      => null,
        ]);

        return redirect()->back()->with('warning', 'Status berkas berhasil direset ke Pending.');
    }

    // Method: update
    public function update(Request $request, int $id): RedirectResponse
    {
        $submission = Submission::query()
            ->with(['department', 'user'])
            ->findOrFail($id);

        $user = auth()->guard()->user();
        $db = app('db');
        $disk = $this->storageDisk();

        if (
            $user->hasRole('operator') &&
            $submission->department_id !== $user->department_id
        ) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data pengajuan instansi lain.');
        }

        if (
            $submission->expires_at &&
            Carbon::now()->greaterThan($submission->expires_at)
        ) {
            if ($submission->status !== 'expired') {
                $submission->update(['status' => 'expired']);
                $submission->documents()->update(['status_dokumen' => 'expired']);
            }

            return redirect()
                ->route('submissions.index')
                ->with('error', 'Gagal memperbarui! Pengajuan ini sudah melewati batas masa kedaluwarsa 3 hari.');
        }

        if ($submission->status === 'expired') {
            return redirect()
                ->route('submissions.index')
                ->with('error', 'Pengajuan ini sudah kedaluwarsa dan tidak dapat diubah.');
        }

        if (in_array($submission->status, ['verified', 'approved'])) {
            return redirect()
                ->route('submissions.index')
                ->with('error', 'Pengajuan yang sudah diverifikasi/disetujui tidak dapat diubah kembali.');
        }

        $isDetailRevision =
            $request->hasFile('files') &&
            !$request->filled('tanggal_spp') &&
            !$request->filled('tanggal_spm');

        if ($isDetailRevision) {
            $request->merge([
                'nomor_spp'       => $submission->nomor_spp,
                'tanggal_spp'     => $submission->tanggal_spp,
                'nomor_spm'       => $submission->nomor_spm,
                'tanggal_spm'     => $submission->tanggal_spm,
                'total_pencairan' => $submission->total_pencairan,
            ]);
        }

        $documentMaxSizeMb = (int) app(SystemSettingsService::class)->getTyped('submission.document_max_size_mb');
        $documentMaxSizeKb = $documentMaxSizeMb * 1024;

        $request->validate([
            'nomor_spp'       => 'required|numeric',
            'nomor_spm'       => 'required|numeric',
            'tanggal_spp'     => 'required|date',
            'tanggal_spm'     => 'required|date',
            'total_pencairan' => 'required|numeric',
            'files'           => 'nullable|array',
            'files.*'         => 'nullable|mimes:pdf|max:' . $documentMaxSizeKb,
        ]);

        $db->beginTransaction();

        try {
            $submission->update([
                'nomor_spp'        => $request->input('nomor_spp'),
                'tanggal_spp'      => $request->input('tanggal_spp'),
                'nomor_spm'        => $request->input('nomor_spm'),
                'tanggal_spm'      => $request->input('tanggal_spm'),
                'total_pencairan'  => $request->input('total_pencairan'),
                'status'           => 'direvisi',
                'verified_by_name' => null,
                'verified_at'      => null,
                'revised_at'       => now(),
            ]);

            if ($request->hasFile('files')) {
                $files = $request->file('files');

                if (is_array($files)) {
                    foreach ($files as $docId => $file) {
                        if (!$file || !$file->isValid()) {
                            continue;
                        }

                        $document = Document::query()
                            ->where('id', $docId)
                            ->where('submission_id', $submission->id)
                            ->firstOrFail();

                        $safeNomorSpm = Str::slug($request->input('nomor_spm'));

                        $fileName = strtoupper($document->jenis_dokumen)
                            . '_'
                            . $safeNomorSpm
                            . '_'
                            . time()
                            . '.'
                            . $file->getClientOriginalExtension();

                        $folderPath = 'arsip/'
                            . date('Y/m')
                            . '/'
                            . Str::slug($submission->department->nama_dinas ?? 'skpd');

                        if (
                            !empty($document->file_path) &&
                            Storage::disk($disk)->exists($document->file_path)
                        ) {
                            Storage::disk($disk)->delete($document->file_path);
                        }

                        $path = $file->storeAs($folderPath, $fileName, $disk);

                        if (!$path) {
                            throw new \RuntimeException("Gagal mengunggah revisi dokumen {$document->jenis_dokumen}.");
                        }

                        $document->update([
                            'file_path'      => $path,
                            'status_dokumen' => 'direvisi',
                        ]);
                    }
                }
            }

            $additionalData = [
                'submission_id'   => $submission->id,
                'nama_operator'   => $user->name,
                'nama_dinas'      => $submission->department->nama_dinas ?? '-',
                'nomor_spm'       => $request->input('nomor_spm'),
                'nomor_spp'       => $request->input('nomor_spp'),
                'total_pencairan' => $request->input('total_pencairan'),
                'status'          => 'direvisi',
            ];

            $targetRoles = [
                'kasie_bud',
                'super_admin'
            ];

            $recipients = User::whereHas('roles', function ($q) use ($targetRoles) {
                $q->whereIn('name', $targetRoles);
            })->get();

            if ($recipients->isNotEmpty()) {
                Notification::send(
                    $recipients,
                    new SystemActivityNotification(
                        'Perbaikan Dokumen',
                        'Revisi SPM No. '
                        . $request->input('nomor_spm')
                        . ' dari '
                        . ($submission->department->nama_dinas ?? 'Instansi')
                        . ' siap ditinjau kembali.',
                        'fa-sync',
                        'warning',
                        $additionalData
                    )
                );
            }

            $db->commit();

            return back()->with(
                'success',
                'Revisi dikirim ulang ke verifikator. Mohon menunggu proses verifikasi ulang.'
            );

        } catch (\Throwable $e) {
            $db->rollBack();

            Log::error('Gagal Update Revisi iDJ: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Gagal memproses perbaikan: ' . $e->getMessage());
        }
    }

    // Method: uploadSpj
    public function uploadSpj(Request $request, int $id): RedirectResponse
    {
        $documentMaxSizeMb = (int) app(SystemSettingsService::class)->getTyped('submission.document_max_size_mb');
        $request->validate([
            'spj_file' => 'required|file|mimes:pdf|max:' . ($documentMaxSizeMb * 1024),
        ]);

        $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
        $user = auth()->guard()->user();
        $disk = $this->storageDisk();

        if ($user->hasRole('operator') && $submission->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk pengajuan instansi lain.');
        }

        if ($submission->jenis_pencairan !== 'TU Nihil') {
            return back()->with('error', 'Upload SPJ lanjutan hanya berlaku untuk TU Nihil.');
        }

        if (in_array($submission->status, ['expired', 'disetujui', 'approved'], true)) {
            return back()->with('error', 'Pengajuan ini tidak dapat menerima SPJ tambahan.');
        }

        $file = $request->file('spj_file');
        $existingDocument = $submission->documents->first(
            fn (Document $document) => $this->documentMatchesType($document->jenis_dokumen, 'SPJ')
        );
        $oldPath = $existingDocument?->file_path;

        try {
            $folder = 'arsip/' . date('Y/m') . '/' . Str::slug($submission->department->nama_dinas ?? 'skpd');
            $path = $file->storeAs(
                $folder,
                'DOKUMEN_SPJ_' . Str::slug($submission->nomor_spm) . '_' . $submission->id . '_' . Str::random(8) . '.pdf',
                $disk
            );

            if (! $path) {
                throw new \RuntimeException('Gagal menyimpan SPJ.');
            }

            if ($existingDocument) {
                $existingDocument->update([
                    'file_path'      => $path,
                    'status_dokumen' => 'uploaded',
                    'catatan'        => null,
                ]);
            } else {
                Document::create([
                    'submission_id'  => $submission->id,
                    'jenis_dokumen'  => 'DOKUMEN SPJ',
                    'file_path'      => $path,
                    'status_dokumen' => 'uploaded',
                    'catatan'        => null,
                ]);
            }

            if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
                Storage::disk($disk)->delete($oldPath);
            }

            $this->notifyDocumentUploaded($submission, 'DOKUMEN SPJ');

            return back()->with('success', 'Dokumen SPJ berhasil diunggah dan menunggu verifikasi.');
        } catch (\Throwable $exception) {
            Log::error('Gagal Upload Dokumen SPJ', [
                'submission_id' => $submission->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Gagal mengunggah Dokumen SPJ. Silakan coba kembali.');
        }
    }

    // Method: uploadSts
    public function uploadSts(Request $request, int $id): RedirectResponse
    {
        $documentMaxSizeMb = (int) app(SystemSettingsService::class)->getTyped('submission.document_max_size_mb');
        $request->validate([
            'sts_file' => 'required|file|mimes:pdf|max:' . ($documentMaxSizeMb * 1024),
        ]);

        $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
        $user = auth()->guard()->user();
        $disk = $this->storageDisk();

        if ($user->hasRole('operator') && $submission->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk pengajuan instansi lain.');
        }

        if ($submission->jenis_pencairan !== 'TU Nihil') {
            return back()->with('error', 'Upload STS lanjutan hanya berlaku untuk TU Nihil.');
        }

        if (in_array($submission->status, ['expired', 'disetujui', 'approved'], true)) {
            return back()->with('error', 'Pengajuan ini tidak dapat menerima STS tambahan.');
        }

        $file = $request->file('sts_file');
        $existingDocument = $submission->documents->first(
            fn (Document $document) => $this->documentMatchesType($document->jenis_dokumen, 'STS')
        );
        $oldPath = $existingDocument?->file_path;

        try {
            $folder = 'arsip/' . date('Y/m') . '/' . Str::slug($submission->department->nama_dinas ?? 'skpd');
            $path = $file->storeAs(
                $folder,
                'STS_' . Str::slug($submission->nomor_spm) . '_' . $submission->id . '_' . Str::random(8) . '.pdf',
                $disk
            );

            if (! $path) {
                throw new \RuntimeException('Gagal menyimpan STS.');
            }

            if ($existingDocument) {
                $existingDocument->update([
                    'file_path'      => $path,
                    'status_dokumen' => 'uploaded',
                    'catatan'        => null,
                ]);
            } else {
                Document::create([
                    'submission_id'  => $submission->id,
                    'jenis_dokumen'  => 'STS',
                    'file_path'      => $path,
                    'status_dokumen' => 'uploaded',
                    'catatan'        => null,
                ]);
            }

            if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
                Storage::disk($disk)->delete($oldPath);
            }

            $this->notifyDocumentUploaded($submission, 'STS');

            return back()->with('success', 'Dokumen STS berhasil diunggah dan menunggu verifikasi.');
        } catch (\Throwable $exception) {
            Log::error('Gagal Upload Dokumen STS', [
                'submission_id' => $submission->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Gagal mengunggah Dokumen STS. Silakan coba kembali.');
        }
    }

    // Method: uploadSp2d
    public function uploadSp2d(Request $request, $id): RedirectResponse
    {
        $sp2dMaxSizeMb = (int) app(SystemSettingsService::class)->getTyped('submission.sp2d_max_size_mb');
        $request->validate([
            'sp2d_file' => 'required|mimes:pdf|max:' . ($sp2dMaxSizeMb * 1024),
        ]);

        $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
        $user = auth()->guard()->user();
        $disk = $this->storageDisk();

        if ($user->hasRole('operator') && $submission->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($submission->jenis_pencairan === 'GU Nihil') {
            return back()->with('error', 'GU Nihil tidak menggunakan dokumen SP2D.');
        }

        if ($submission->jenis_pencairan === 'TU Nihil') {
            return back()->with('error', 'SP2D TU Nihil wajib diinput pada pengajuan awal, bukan melalui upload SP2D lanjutan.');
        }

        if (! in_array($submission->jenis_pencairan, ['LS', 'GU', 'TU', 'UP'], true)) {
            return back()->with('error', 'Jenis pencairan ini tidak menggunakan SP2D.');
        }

        if ($submission->status === 'expired') {
            return back()->with('error', 'Pengajuan ini sudah kedaluwarsa.');
        }

        if (! in_array($submission->status, ['disetujui', 'approved'], true)) {
            return back()->with('error', 'SP2D hanya dapat diunggah setelah pengajuan disetujui.');
        }

        if ($request->hasFile('sp2d_file')) {
            $file = $request->file('sp2d_file');
            $filename = 'sp2d_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents/sp2d', $filename, $disk);

            if (! $path) {
                return back()->with('error', 'Gagal mengunggah dokumen SP2D ke storage.');
            }

            $document = $submission->documents->first(
                fn (Document $item) => $this->documentMatchesType($item->jenis_dokumen, 'SP2D')
            );

            $oldPath = $document?->file_path;
            $newStatus = $document ? 'direvisi' : 'uploaded';

            if ($document) {
                $document->update([
                    'file_path'      => $path,
                    'status_dokumen' => $newStatus,
                    'catatan'        => null,
                ]);

                if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
                    Storage::disk($disk)->delete($oldPath);
                }
            } else {
                Document::create([
                    'submission_id'  => $submission->id,
                    'jenis_dokumen'  => 'SP2D',
                    'file_path'      => $path,
                    'status_dokumen' => $newStatus,
                    'catatan'        => null,
                ]);
            }

            $submission->status_sp2d = $newStatus;
            $submission->save();

            $this->notifyDocumentUploaded($submission, 'SP2D');
        }

        return redirect()->back()->with('success', 'Dokumen SP2D berhasil diunggah dan status diperbarui.');
    }

    // Method: verifikasiSp2d
    public function verifikasiSp2d(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'action_type' => 'required|in:approve,reject',
            'catatan'     => 'required_if:action_type,reject|string|max:500',
        ], [
            'catatan.required_if' => 'Catatan penolakan wajib diisi agar operator mengetahui bagian yang harus diperbaiki.',
        ]);

        $submission = Submission::with(['department', 'documents'])->findOrFail($id);

        if (! in_array($submission->jenis_pencairan, ['LS', 'GU', 'TU', 'UP'], true)) {
            return back()->with('error', 'Jenis pencairan ini tidak menggunakan SP2D lanjutan.');
        }

        if (! in_array($submission->status, ['disetujui', 'approved'], true)) {
            return back()->with('error', 'SP2D lanjutan hanya dapat diverifikasi setelah pengajuan disetujui.');
        }

        $document = $submission->documents->first(
            fn (Document $item) => $this->documentMatchesType($item->jenis_dokumen, 'SP2D')
        );

        if (! $document) {
            return back()->with('error', 'Dokumen SP2D belum tersedia.');
        }

        $operators = User::where('department_id', $submission->department_id)
            ->whereHas('roles', fn ($query) => $query->where('name', 'operator'))
            ->get();

        if ($request->action_type === 'approve') {
            $document->update([
                'status_dokumen' => 'verified',
                'catatan'        => null,
            ]);
            $submission->status_sp2d = 'lengkap';
            $submission->save();

            $additionalData = [
                'submission_id' => $submission->id,
                'nomor_spm'     => $submission->nomor_spm,
                'status_sp2d'   => 'lengkap',
            ];

            foreach ($operators as $operator) {
                $operator->notify(new SystemActivityNotification(
                    'SP2D Disetujui',
                    'SP2D dengan No. SPM ' . $submission->nomor_spm . ' telah disetujui dan diverifikasi.',
                    'fa-check-circle',
                    'success',
                    $additionalData
                ));
            }

            return back()->with('success', 'SP2D berhasil disetujui dan diverifikasi.');
        }

        $catatan = $request->input('catatan');
        $document->update([
            'status_dokumen' => 'revisi',
            'catatan'        => $catatan,
        ]);
        $submission->status_sp2d = 'revisi';
        $submission->save();

        $additionalData = [
            'submission_id' => $submission->id,
            'nomor_spm'     => $submission->nomor_spm,
            'status_sp2d'   => 'revisi',
            'catatan'       => $catatan,
        ];

        foreach ($operators as $operator) {
            $operator->notify(new SystemActivityNotification(
                'SP2D Perlu Perbaikan',
                'No. SPM ' . $submission->nomor_spm . '. Catatan: "' . $catatan . '". Silakan lakukan perbaikan dan kirim kembali.',
                'fa-exclamation-triangle',
                'danger',
                $additionalData
            ));
        }

        return back()->with('error', 'SP2D ditolak dan dikembalikan ke operator untuk perbaikan.');
    }

    // Method: destroy
    public function destroy(int $id): RedirectResponse
    {
        $submission = Submission::query()->with(['documents', 'department'])->findOrFail($id);
        $user = auth()->guard()->user();
        $disk = $this->storageDisk();

        if ($user->hasRole('operator')) {
            if ($submission->department_id !== $user->department_id) {
                abort(403, 'Unauthorized action.');
            }
            if (!in_array(strtolower($submission->status), ['pending', 'expired'])) {
                return redirect()->route('submissions.index')
                    ->with('error', 'Pengajuan yang sudah diproses atau disetujui tidak dapat dihapus oleh Operator.');
            }
        } elseif ($user->hasRole('super_admin')) {
        } else {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            $submissionArray = $submission->toArray();
            $noSpm = $submissionArray['nomor_spm'] ?? 'ID #' . $submission->id;
            $namaDinas = $submission->department->nama_dinas ?? $submission->department->name ?? 'Dinas Terkait';

            DB::table('submission_logs')->insert([
                'submission_id'   => $submission->id,
                'user_id'         => $user->id,
                'action'          => 'DELETED',
                'submission_data' => json_encode($submissionArray),
                'documents_data'  => json_encode($submission->documents->toArray()),
                'deleted_at'      => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            foreach ($submission->documents as $document) {
                if (!empty($document->file_path) && Storage::disk($disk)->exists($document->file_path)) {
                    Storage::disk($disk)->delete($document->file_path);
                }
            }

            $submission->documents()->delete();
            $submission->delete();

            DB::commit();

            $superAdmins = User::role('super_admin')->get();
            $recipients = $superAdmins->push($user)->unique('id');

            $title = 'Pengajuan Dihapus';
            $message = "Pengajuan SPM nomor ({$noSpm}) dari instansi **{$namaDinas}** (Status: {$submission->status}) telah dihapus oleh " . $user->name;
            $icon = 'fas fa-trash-alt';
            $type = 'danger';
            $additionalData = [
                'submission_id' => $submission->id,
                'nomor_spm'     => $noSpm,
                'department'    => $namaDinas,
                'deleted_by'    => $user->name,
                'url'           => route('submissions.index'),
            ];

            Notification::send(
                $recipients,
                new SystemActivityNotification($title, $message, $icon, $type, $additionalData)
            );

            return redirect()->route('submissions.index')
                ->with('success', 'Data pengajuan berhasil dihapus, histori tercatat, dan notifikasi terkirim.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal Hapus Pengajuan iDJ: ' . $e->getMessage());

            return redirect()->route('submissions.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // Method: backup
    public function backup(Request $request)
    {
        $user = auth()->guard()->user();

        $query = Submission::query()->with(['department', 'user', 'documents']);

        if ($user->hasRole('operator')) {
            $query->where('department_id', $user->department_id);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->input('tahun'));
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'backup_pengajuan_lengkap_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($submissions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID Submission', 'Nama Dinas', 'Nomor SPP', 'Tanggal SPP',
                'Nomor SPM', 'Tanggal SPM', 'Jenis Pencairan', 'Sumber Dana',
                'Judul Kegiatan', 'Judul Sub Kegiatan', 'Perihal', 'Total Pencairan',
                'Status Pengajuan', 'Jenis Dokumen', 'Status Dokumen', 'Path File Upload', 'Tanggal Dibuat'
            ]);

            foreach ($submissions as $item) {
                if ($item->documents->isNotEmpty()) {
                    foreach ($item->documents as $doc) {
                        fputcsv($file, [
                            $item->id,
                            $item->department->nama_dinas ?? '-',
                            $item->nomor_spp,
                            $item->tanggal_spp,
                            $item->nomor_spm,
                            $item->tanggal_spm,
                            $item->jenis_pencairan,
                            $item->sumber_dana,
                            $item->judul_kegiatan,
                            $item->judul_sub_kegiatan,
                            $item->perihal,
                            $item->total_pencairan,
                            $item->status,
                            strtoupper($doc->jenis_dokumen),
                            strtoupper($doc->status_dokumen),
                            $doc->file_path ?? '-',
                            $item->created_at,
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        $item->id,
                        $item->department->nama_dinas ?? '-',
                        $item->nomor_spp,
                        $item->tanggal_spp,
                        $item->nomor_spm,
                        $item->tanggal_spm,
                        $item->jenis_pencairan,
                        $item->sumber_dana,
                        $item->judul_kegiatan,
                        $item->judul_sub_kegiatan,
                        $item->perihal,
                        $item->total_pencairan,
                        $item->status,
                        '-',
                        '-',
                        '-',
                        $item->created_at,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Method: downloadPdfBundle
    public function downloadPdfBundle(int $id)
    {
        $submission = Submission::query()->with(['department', 'user', 'documents'])->findOrFail($id);

        $pdfContent = view('submissions.pdf-bundle', compact('submission'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($pdfContent);
        $pdfOutput = $pdf->output();

        $zip = new \ZipArchive;
        $zipFileName = 'REKAP TRANSAKSI SPM NO. ' . $submission->nomor_spm . '.zip';
        // Gunakan direktori temporary sistem agar aman di Wasmer Edge
        $zipPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . uniqid('zip_', true) . '_' . $zipFileName;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFromString('RINGKASAN TRANSAKSI SPM NO. ' . $submission->nomor_spm . '.pdf', $pdfOutput);
            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}