<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\SystemActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ValidasiController extends Controller
{

    // Method: index
    public function index(Request $request)
    {
        $query = Submission::with([
                'department',
                'user',
                'documents',
                'penolakKabid'
            ])
            ->where('status', 'proses validasi');

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('nomor_spp', 'like', "%{$searchTerm}%")
                    ->orWhere('nomor_spm', 'like', "%{$searchTerm}%")
                    ->orWhereHas('department', function ($dq) use ($searchTerm) {
                        $dq->where('nama_dinas', 'like', "%{$searchTerm}%")
                            ->orWhere('kode_dinas', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pencairan', $request->jenis);
        }

        $sortBy = $request->get('sort', 'updated_at');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = [
            'tanggal',
            'nomor_spp',
            'nomor_spm',
            'departments.nama_dinas',
            'judul_kegiatan',
            'judul_sub_kegiatan',
            'perihal',
            'total_pencairan',
            'jenis_pencairan',
            'sumber_dana',
            'status',
            'updated_at'
        ];

        if (in_array($sortBy, $allowedSorts)) {

            if ($sortBy === 'departments.nama_dinas') {

                $query->select('submissions.*')
                    ->join(
                        'departments',
                        'submissions.department_id',
                        '=',
                        'departments.id'
                    )
                    ->orderBy(
                        'departments.nama_dinas',
                        $direction
                    );

            } else {

                $query->orderBy(
                    $sortBy,
                    $direction
                );
            }

        } else {

            $query->oldest('updated_at');
        }

        $submissions = $query
            ->paginate(10)
            ->withQueryString();

        $departments = \App\Models\Department::orderBy(
            'nama_dinas',
            'asc'
        )->get();

        return view(
            'validasi.index',
            compact(
                'submissions',
                'departments',
                'sortBy',
                'direction'
            )
        );
    }

// Method: show
    public function show($id)
    {
        $submission = Submission::with([
            'department',
            'documents',
            'user',
            'penolakKabid'
        ])->findOrFail($id);

        // Proteksi: Hanya pengajuan dengan status 'proses validasi' yang boleh dibuka di halaman validasi
        if (strtolower(trim($submission->status ?? '')) !== 'proses validasi') {
            return redirect()
                ->route('validasi.index')
                ->with(
                    'warning',
                    'Pengajuan ini sudah selesai divalidasi (Status: ' . strtoupper($submission->status) . ') dan tidak dapat diakses kembali melalui halaman validasi.'
                );
        }

        return view(
            'validasi.show',
            compact('submission')
        );
    }

    // Method: getRequiredDocumentKeys
    private function getRequiredDocumentKeys($submission): array
    {
        $jenis = $this->normalizeJenisPencairan(
            $submission->jenis_pencairan
        );

        return match ($jenis) {

            'LS' => [
                'DOKUMEN LS',
                'DOKUMEN SPJ',
            ],

            'TU' => [
                'DOKUMEN TU',
            ],

            'GU' => [
                'DOKUMEN GU',
                'DOKUMEN SPJ',
            ],

            'UP' => [
                'DOKUMEN UP',
            ],

            'TU NIHIL' => [
                'DOKUMEN SPJ',
                'SP2D',
            ],

            'GU NIHIL' => [
                'DOKUMEN SPJ',
            ],

            default => [],
        };
    }

    // Method: normalizeJenisPencairan
    private function normalizeJenisPencairan($jenis): string
    {
        $jenis = strtoupper(
            trim((string) $jenis)
        );

        $jenis = str_replace(
            ['-', '_', '/', '\\'],
            ' ',
            $jenis
        );

        $jenis = preg_replace(
            '/\s+/',
            ' ',
            $jenis
        );

        return trim($jenis);
    }

    // Method: normalizeDocumentType
    private function normalizeDocumentType($document): string
    {

        $rawType = $document->jenis_dokumen
            ?? $document->jenis_document
            ?? '';

        $type = strtoupper(
            trim((string) $rawType)
        );

        $type = str_replace(
            ['-', '_', '/', '\\'],
            ' ',
            $type
        );

        $type = preg_replace(
            '/\s+/',
            ' ',
            $type
        );

        $type = trim($type);

        if (
            $type === 'DOKUMEN TU NIHIL' ||
            $type === 'TU NIHIL'
        ) {
            return 'DOKUMEN TU NIHIL';
        }

        if (
            $type === 'DOKUMEN GU NIHIL' ||
            $type === 'GU NIHIL'
        ) {
            return 'DOKUMEN GU NIHIL';
        }

        if (
            $type === 'DOKUMEN LS' ||
            $type === 'LS'
        ) {
            return 'DOKUMEN LS';
        }

        if (
            $type === 'DOKUMEN TU' ||
            $type === 'TU'
        ) {
            return 'DOKUMEN TU';
        }

        if (
            $type === 'DOKUMEN GU' ||
            $type === 'GU'
        ) {
            return 'DOKUMEN GU';
        }

        if (
            $type === 'DOKUMEN UP' ||
            $type === 'UP'
        ) {
            return 'DOKUMEN UP';
        }

        if (
            $type === 'DOKUMEN SPJ' ||
            $type === 'SPJ'
        ) {
            return 'DOKUMEN SPJ';
        }

        if (
            $type === 'DOKUMEN STS' ||
            $type === 'STS'
        ) {
            return 'STS';
        }

        if (
            $type === 'DOKUMEN SP2D' ||
            $type === 'SP2D'
        ) {
            return 'SP2D';
        }

        return $type;
    }

    // Method: getValidationDocuments
    private function getValidationDocuments($submission): array
    {
        $requiredKeys = $this->getRequiredDocumentKeys(
            $submission
        );

        $normalizedDocuments = collect();

        foreach ($submission->documents as $document) {

            $normalizedType = $this->normalizeDocumentType(
                $document
            );

            if (!$normalizedDocuments->has($normalizedType)) {

                $normalizedDocuments->put(
                    $normalizedType,
                    $document
                );
            }
        }

        return [
            'requiredKeys' => $requiredKeys,
            'documents'    => $normalizedDocuments,
        ];
    }

    // Method: checkValidationCompleteness
    private function checkValidationCompleteness($submission): array
    {
        $data = $this->getValidationDocuments(
            $submission
        );

        $requiredKeys = $data['requiredKeys'];
        $documents    = $data['documents'];

        $missing = [];
        $notVerified = [];

        foreach ($requiredKeys as $requiredKey) {

            if (!$documents->has($requiredKey)) {

                $missing[] = $requiredKey;

                continue;
            }

            $document = $documents->get(
                $requiredKey
            );

            if (
                !$document ||
                $document->status_dokumen !== 'verified'
            ) {
                $notVerified[] = $requiredKey;
            }
        }

        return [
            'is_complete' =>
                count($missing) === 0 &&
                count($notVerified) === 0,

            'required'     => $requiredKeys,
            'missing'      => $missing,
            'not_verified' => $notVerified,
        ];
    }

// Method: sahkan
    public function sahkan($id)
    {
        $validator = auth()->guard()->user();

        return DB::transaction(function () use (
            $id,
            $validator
        ) {
            $submission = Submission::with([
                'user',
                'department',
                'documents'
            ])
            ->lockForUpdate()
            ->findOrFail($id);

            if (strtolower(trim($submission->status ?? '')) !== 'proses validasi') {
                return redirect()
                    ->route('validasi.index')
                    ->with(
                        'error',
                        'Pengajuan tidak berada pada tahap validasi.'
                    );
            }

            $check = $this->checkValidationCompleteness($submission);

            if (!$check['is_complete']) {
                $messages = [];

                if (count($check['missing']) > 0) {
                    $messages[] =
                        'Dokumen wajib belum diunggah: ' .
                        implode(', ', $check['missing']);
                }

                if (count($check['not_verified']) > 0) {
                    $messages[] =
                        'Dokumen wajib belum diverifikasi: ' .
                        implode(', ', $check['not_verified']);
                }

                return redirect()
                    ->route('validasi.show', $submission->id)
                    ->with('error', implode(' ', $messages));
            }

            $submission->update([
                'status'            => 'disetujui',
                'validated_by'      => $validator->id,
                'validated_by_name' => $validator->name,
                'validated_at'      => now(),
            ]);

            $jenis = $this->normalizeJenisPencairan(
                $submission->jenis_pencairan
            );

            $requiresFinalSp2d = in_array(
                $jenis,
                ['LS', 'TU', 'GU'],
                true
            );

            if ($submission->user) {
                $additionalData = [
                    'submission_id'   => $submission->id,
                    'validator_nama'  => $validator->name,
                    'nama_operator'   => $submission->user->name ?? '-',
                    'nama_dinas'      => $submission->department->nama_dinas ?? '-',
                    'nomor_spm'       => $submission->nomor_spm,
                    'nomor_spp'       => $submission->nomor_spp,
                    'total_pencairan' => $submission->total_pencairan,
                    'status'          => 'disetujui',
                ];

                if ($requiresFinalSp2d) {
                    $notificationMessage =
                        'Pengajuan transaksi dari ' .
                        ($submission->department->nama_dinas ?? 'SKPD') .
                        ' telah divalidasi. Silakan unggah dokumen SP2D untuk melengkapi proses.';
                } else {
                    $notificationMessage =
                        'Pengajuan transaksi dari ' .
                        ($submission->department->nama_dinas ?? 'SKPD') .
                        ' telah divalidasi dan disetujui.';
                }

                $submission->user->notify(
                    new SystemActivityNotification(
                        'SPM No. ' .
                        $submission->nomor_spm .
                        ' Telah Divalidasi',
                        $notificationMessage,
                        'fa-check-circle',
                        'success',
                        $additionalData
                    )
                );
            }

            return redirect()
                ->route('validasi.index')
                ->with(
                    'success',
                    'Pengajuan transaksi berhasil divalidasi dan disetujui.'
                );
        });
    }

// Method: tolak
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|min:5',
        ]);

        $validator = auth()->guard()->user();

        return DB::transaction(function () use (
            $request,
            $id,
            $validator
        ) {
            $submission = Submission::with([
                'user',
                'department'
            ])
            ->lockForUpdate()
            ->findOrFail($id);

            // Proteksi: Cegah penolakan ulang jika status sudah berubah dari 'proses validasi'
            if (strtolower(trim($submission->status ?? '')) !== 'proses validasi') {
                return redirect()
                    ->route('validasi.index')
                    ->with(
                        'error',
                        'Pengajuan ini sudah selesai diproses dan tidak dapat ditolak lagi.'
                    );
            }

            $submission->update([
                'status'         => 'rejected',
                'catatan_kabid'  => $request->catatan,
                'rejected_at'    => now(),
                'tolak_by_kabid' => $validator->id,
            ]);

            $submission->documents()->update([
                'status_dokumen' => 'rejected',
                'catatan' =>
                    'Ditolak Kabid (Wajib Upload Ulang): ' .
                    $request->catatan,
            ]);

            $additionalData = [
                'submission_id'   => $submission->id,
                'validator_nama'  => $validator->name,
                'nama_operator'   => $submission->user->name ?? '-',
                'nama_dinas'      => $submission->department->nama_dinas ?? '-',
                'nomor_spm'       => $submission->nomor_spm,
                'nomor_spp'       => $submission->nomor_spp,
                'total_pencairan' => $submission->total_pencairan,
                'catatan_tolak'   => $request->catatan,
                'status'          => 'rejected',
            ];

            if ($submission->user) {
                $submission->user->notify(
                    new SystemActivityNotification(
                        'SPM No. ' .
                        $submission->nomor_spm .
                        ' Perlu Perbaikan',
                        'Permintaan ditolak. Catatan: "' .
                        $request->catatan .
                        '". Silakan lakukan perbaikan berkas dan ajukan kembali.',
                        'fa-times-circle',
                        'danger',
                        $additionalData
                    )
                );
            }

            return redirect()
                ->route('validasi.index')
                ->with(
                    'error',
                    'Pengajuan transaksi telah dikembalikan untuk diperbaiki oleh operator SKPD.'
                );
        });
    }

    
    // Method: previewPdf
    public function previewPdf($id)
    {
        $document = \App\Models\Document::findOrFail($id);

        $path = $document->file_path;

        if (!Storage::disk('public')->exists($path)) {

            abort(
                404,
                'File PDF dokumen tidak ditemukan di storage sistem.'
            );
        }

        return Storage::disk('public')->response(
            $path,
            'Dokumen-Pratinjau.pdf',
            [
                'Content-Type' =>
                    'application/pdf',

                'Content-Disposition' =>
                    'inline; filename="Dokumen-Pratinjau.pdf"'
            ]
        );
    }
}
