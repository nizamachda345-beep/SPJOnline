<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Document;
use App\Models\User;
use App\Notifications\SystemActivityNotification;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{

    // Method: index
    public function index(Request $request)
    {
        return $this->listing($request, false);
    }

    // Method: sp2dIndex
    public function sp2dIndex(Request $request)
    {
        return $this->listing($request, true);
    }

    // Method: listing
    private function listing(Request $request, bool $isSp2dQueue)
    {
        $query = Submission::query()
            ->select('submissions.*')
            ->join(
                'departments',
                'submissions.department_id',
                '=',
                'departments.id'
            )
            ->with([
                'department',
                'user'
            ])
            ->where(function ($q) use ($isSp2dQueue) {
                if ($isSp2dQueue) {
                    $q->whereIn('submissions.status', ['disetujui', 'approved'])
                        ->whereIn('submissions.status_sp2d', ['uploaded', 'direvisi'])
                        ->where(function ($type) {
                            $type->whereNull('submissions.jenis_pencairan')
                                ->orWhereRaw("UPPER(TRIM(submissions.jenis_pencairan)) NOT IN ('TU NIHIL', 'TUNIHIL')");
                        });

                    return;
                }

                $q->whereIn('submissions.status', ['pending', 'direvisi']);
            });

        $query->when(
            $request->nomor,
            function ($q) use ($request) {

                $search = $request->nomor;

                $q->where(function ($inner) use ($search) {

                    $inner->where(
                        'nomor_spp',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'nomor_spm',
                        'like',
                        '%' . $search . '%'
                    );
                });
            }
        );

        $query->when(
            $request->tanggal,
            function ($q) use ($request) {

                $q->whereDate(
                    'submissions.created_at',
                    $request->tanggal
                );
            }
        );

        $query->when(
            $request->jenis_pencairan,
            function ($q) use ($request) {

                $q->where(
                    'jenis_pencairan',
                    $request->jenis_pencairan
                );
            }
        );

        $sortBy = $request->get(
            'sort',
            'tanggal'
        );

        $direction = $request->get(
            'direction',
            'desc'
        );

        $direction = in_array(
            $direction,
            [
                'asc',
                'desc'
            ]
        )
            ? $direction
            : 'desc';

        switch ($sortBy) {

            case 'departments.nama_dinas':

                $column = 'departments.nama_dinas';

                break;

            case 'nominal':

                $column = 'nominal';

                break;

            case 'tanggal':

            default:

                $column = 'submissions.created_at';

                break;
        }

        $submissions = $query
            ->orderBy(
                $column,
                $direction
            )
            ->paginate(10)
            ->withQueryString();

        $listRouteName = $isSp2dQueue ? 'verifikasi.sp2d' : 'verifikasi.index';

        return view('verifikasi.index', compact(
            'submissions',
            'sortBy',
            'direction',
            'isSp2dQueue',
            'listRouteName'
        ));
    }

    // Method: show
    public function show(Request $request, $id)
    {
        $submission = Submission::with([
            'department',
            'documents'
        ])->findOrFail($id);

        $isSp2dVerificationMode =
            in_array(
                strtolower(trim($submission->status ?? '')),
                ['disetujui', 'approved'],
                true
            )
            &&
            in_array(
                strtolower(
                    trim(
                        $submission->status_sp2d ?? ''
                    )
                ),
                [
                    'uploaded',
                    'direvisi'
                ],
                true
            )
            &&
            !in_array(
                $this->normalizeDocumentType(
                    $submission->jenis_pencairan
                ),
                [
                    'tu nihil',
                    'tunihil'
                ],
                true
            );

        if ($isSp2dVerificationMode) {

            $requiredDocumentIds = [];

            $totalDocs = 1;

            $verifiedDocs = 0;

            $isAllVerified = false;
            $backRouteName = $request->query('source') === 'sp2d'
                ? 'verifikasi.sp2d'
                : 'verifikasi.index';

            return view(
                'verifikasi.show',
                compact(
                    'submission',
                    'requiredDocumentIds',
                    'totalDocs',
                    'verifiedDocs',
                    'isAllVerified',
                    'isSp2dVerificationMode',
                    'backRouteName'
                )
            );
        }

        $requiredDocumentIds =
            $this->getRequiredDocumentIds(
                $submission
            );

        $requiredDocuments =
            $submission->documents
                ->whereIn(
                    'id',
                    $requiredDocumentIds
                );

        $totalDocs =
            $requiredDocuments->count();

        $verifiedDocs =
            $requiredDocuments
                ->where(
                    'status_dokumen',
                    'verified'
                )
                ->count();

        $isAllVerified = (
            $totalDocs > 0 &&
            $verifiedDocs >= $totalDocs
        );

        $backRouteName = $request->query('source') === 'sp2d'
            ? 'verifikasi.sp2d'
            : 'verifikasi.index';

        return view(
            'verifikasi.show',
            compact(
                'submission',
                'requiredDocumentIds',
                'totalDocs',
                'verifiedDocs',
                'isAllVerified',
                'isSp2dVerificationMode',
                'backRouteName'
            )
        );
    }

    // Method: getRequiredDocumentIds
    private function getRequiredDocumentIds(
        Submission $submission
    ) {
        $jenisPencairan =
            $this->normalizeDocumentType(
                $submission->jenis_pencairan
            );

        $documents =
            $submission->documents;

        $requiredIds =
            collect();

        if ($jenisPencairan === 'ls') {

            foreach ($documents as $doc) {

                $jenis =
                    $this->normalizeDocumentType(
                        $doc->jenis_dokumen
                    );

                if (
                    str_contains(
                        $jenis,
                        'ls'
                    )
                    &&
                    !$this->isSp2dDocument(
                        $jenis
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }

                elseif (
                    str_contains(
                        $jenis,
                        'spj'
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }
            }
        }

        elseif ($jenisPencairan === 'tu') {

            foreach ($documents as $doc) {

                $jenis =
                    $this->normalizeDocumentType(
                        $doc->jenis_dokumen
                    );

                if (
                    str_contains(
                        $jenis,
                        'tu'
                    )
                    &&
                    !str_contains(
                        $jenis,
                        'nihil'
                    )
                    &&
                    !$this->isSp2dDocument(
                        $jenis
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }
            }
        }

        elseif ($jenisPencairan === 'gu') {

            foreach ($documents as $doc) {

                $jenis = $this->normalizeDocumentType(
                    $doc->jenis_dokumen
                );

                if (
                    str_contains($jenis, 'gu')
                    &&
                    !str_contains($jenis, 'nihil')
                    &&
                    !$this->isSp2dDocument($jenis)
                ) {
                    $requiredIds->push($doc->id);
                }

                if (
                    str_contains($jenis, 'spj')
                    &&
                    !$this->isSp2dDocument($jenis)
                ) {
                    $requiredIds->push($doc->id);
                }
            }
        }

        elseif ($jenisPencairan === 'up') {

            foreach ($documents as $doc) {

                $jenis =
                    $this->normalizeDocumentType(
                        $doc->jenis_dokumen
                    );

                if (
                    str_contains(
                        $jenis,
                        'up'
                    )
                    &&
                    !$this->isSp2dDocument(
                        $jenis
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }

            }
        }

        elseif (
            $jenisPencairan === 'tu nihil'
            ||
            $jenisPencairan === 'tunihil'
        ) {

            foreach ($documents as $doc) {

                $jenis =
                    $this->normalizeDocumentType(
                        $doc->jenis_dokumen
                    );

                if (
                    str_contains(
                        $jenis,
                        'spj'
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }

                elseif (
                    $this->isSp2dDocument(
                        $jenis
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }

            }
        }

        elseif (
            $jenisPencairan === 'gu nihil'
            ||
            $jenisPencairan === 'guhil'
        ) {

            foreach ($documents as $doc) {

                $jenis =
                    $this->normalizeDocumentType(
                        $doc->jenis_dokumen
                    );

                if (
                    str_contains(
                        $jenis,
                        'spj'
                    )
                ) {

                    $requiredIds->push(
                        $doc->id
                    );
                }

            }
        }

        return $requiredIds
            ->unique()
            ->values()
            ->toArray();
    }

    // Method: normalizeDocumentType
    private function normalizeDocumentType(
        $value
    ) {
        return strtolower(
            trim(
                preg_replace(
                    '/\s+/',
                    ' ',
                    str_replace(
                        [
                            '_',
                            '-',
                            '/',
                            '\\'
                        ],
                        ' ',
                        (string) $value
                    )
                )
            )
        );
    }

    // Method: isSp2dDocument
    private function isSp2dDocument(
        $jenis
    ) {
        return str_contains(
            $jenis,
            'sp2d'
        );
    }

    // Method: documentAction
    public function documentAction(
        Request $request,
        $docId
    ) {

        $verifier =
            auth()->guard()->user();

        $doc = Document::with([
            'submission.department',
            'submission.user'
        ])->findOrFail(
            $docId
        );

        $submission =
            $doc->submission;

        if (
            $this->isSp2dDocument(
                $this->normalizeDocumentType(
                    $doc->jenis_dokumen
                )
            )
            &&
            !in_array(
                $this->normalizeDocumentType(
                    $submission->jenis_pencairan
                ),
                [
                    'tu nihil',
                    'tunihil'
                ],
                true
            )
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Dokumen SP2D harus diverifikasi melalui proses verifikasi SP2D.'
            ], 422);
        }

        if (
            $request->action === 'setuju'
        ) {

            $doc->update([
                'status_dokumen' => 'verified',
                'catatan' => null
            ]);
        }

        elseif (
            $request->action === 'revisi'
        ) {

            $doc->update([
                'status_dokumen' => 'revisi',
                'catatan' => $request->catatan
            ]);
        }

        if (
            !in_array(
                $submission->status,
                [
                    'disetujui',
                    'selesai',
                    'history'
                ]
            )
        ) {

            $revisiDocsCount =
                $submission
                    ->documents()
                    ->where(
                        'status_dokumen',
                        'revisi'
                    )
                    ->count();

            $submissionPayload = [
                'status' =>
                    $revisiDocsCount > 0
                        ? 'revisi'
                        : 'pending'
            ];

            if (
                $revisiDocsCount > 0
            ) {

                $submissionPayload[
                    'revisi_at'
                ] = now();

                $submissionPayload[
                    'tolak_by'
                ] = $verifier->id;
            }

            $submission->update(
                $submissionPayload
            );
        }

        $additionalData = [

            'submission_id' => $submission->id,

            'verifier_nama' =>
                $verifier->name,

            'nama_dinas' =>
                $submission
                    ->department
                    ->nama_dinas
                ?? '-',

            'nomor_spm' =>
                $submission->nomor_spm,

            'nomor_spp' =>
                $submission->nomor_spp,

            'jenis_dokumen' =>
                $doc->jenis_dokumen,

            'status_dokumen' =>
                $doc->status_dokumen,

            'catatan' =>
                $request->catatan ?? null
        ];

        if (
            $request->action === 'revisi'
            &&
            $submission->user
        ) {

            $submission->user->notify(
                new SystemActivityNotification(

                    'Revisi Dokumen Diperlukan',

                    'SPM No.' .
                    $submission->nomor_spm .
                    ' perlu direvisi. ' .
                    'Instruksi: Periksa catatan dan lakukan perbaikan dokumen.',

                    'fa-exclamation-triangle',

                    'warning',

                    $additionalData
                )
            );
        }

        if (
            $request->ajax()
        ) {

            return response()->json([

                'success' => true,

                'message' =>
                    'Status berkas berhasil diperbarui.',

                'status_dokumen' =>
                    $doc->status_dokumen,

                'submission_status' =>
                    $submission->status
            ]);
        }

        return back()->with(
            'success',
            'Status berkas berhasil diperbarui.'
        );
    }

    // Method: sendToKabid
    public function sendToKabid(
        Request $request,
        $id
    ) {

        $verifier =
            auth()->guard()->user();

        $submission =
            Submission::with([
                'documents',
                'user',
                'department'
            ])->findOrFail(
                $id
            );

        if (
            strtolower(
                trim(
                    $submission->status ?? ''
                )
            ) === 'disetujui'
        ) {

            return back()->with(
                'error',
                'Pengajuan ini sudah disetujui. Verifikasi SP2D dilakukan melalui proses khusus.'
            );
        }

        $requiredDocumentIds =
            $this->getRequiredDocumentIds(
                $submission
            );

        if (
            empty(
                $requiredDocumentIds
            )
        ) {

            return back()->with(
                'error',
                'Gagal! Dokumen wajib untuk jenis pencairan ini belum tersedia.'
            );
        }

        $requiredDocuments =
            $submission
                ->documents
                ->whereIn(
                    'id',
                    $requiredDocumentIds
                );

        $unverifiedDocuments =
            $requiredDocuments
                ->filter(
                    function ($doc) {

                        return $doc->status_dokumen
                            !== 'verified';
                    }
                );

        if (
            $unverifiedDocuments->count() > 0
        ) {

            $namaDokumen =
                $unverifiedDocuments
                    ->map(
                        function ($doc) {

                            return strtoupper(
                                str_replace(
                                    '_',
                                    ' ',
                                    $doc->jenis_dokumen
                                )
                            );
                        }
                    )
                    ->implode(', ');

            return back()->with(
                'error',
                'Gagal! Dokumen wajib belum diverifikasi: ' .
                $namaDokumen
            );
        }

        $submission->update([

            'status' =>
                'proses validasi',

            'verified_by' =>
                $verifier->id,

            'verified_by_name' =>
                $verifier->name,

            'verified_at' =>
                now(),

            'sent_to_kabid_at' =>
                now(),

            'sent_to_kabid_by_name' =>
                $verifier->name
        ]);

        $additionalData = [

            'submission_id' => $submission->id,

            'verifier_nama' =>
                $verifier->name,

            'nama_dinas' =>
                $submission
                    ->department
                    ->nama_dinas
                ?? '-',

            'nomor_spm' =>
                $submission->nomor_spm,

            'nomor_spp' =>
                $submission->nomor_spp,

            'total_pencairan' =>
                $submission->total_pencairan,

            'status' =>
                'proses validasi'
        ];

        if (
            $submission->user
        ) {

            $submission->user->notify(
                new SystemActivityNotification(

                    'Permintaan Terverifikasi',

                    'SPM No.' .
                    $submission->nomor_spm .
                    ' telah diverifikasi. ' .
                    'Instruksi: Menunggu proses Validasi.',

                    'fa-paper-plane',

                    'info',

                    $additionalData
                )
            );
        }

        $targetRoles = [
            'kabid_perbendaharaan',
            'super_admin'
        ];

        $recipients =
            User::whereHas(
                'roles',
                function ($q) use (
                    $targetRoles
                ) {

                    $q->whereIn(
                        'name',
                        $targetRoles
                    );
                }
            )->get();

        foreach (
            $recipients
            as $recipient
        ) {

            $recipient->notify(
                new SystemActivityNotification(

                    'Menunggu Validasi',

                    'SPM No.' .
                    'SPM No.' .
                    $submission->nomor_spm .
                    ' (' .
                    (
                        $submission
                            ->department
                            ->nama_dinas
                        ?? 'SKPD'
                    ) .
                    ') siap diperiksa. ' .
                    'Instruksi: Lakukan validasi dokumen.',

                    'fa-paper-plane',

                    'info',

                    $additionalData
                )
            );
        }

        return redirect()
            ->route(
                'verifikasi.index'
            )
            ->with(
                'success',
                'Berkas berhasil diteruskan untuk Proses Validasi.'
            );
    }

    // Method: tolak
    public function tolak(
        Request $request,
        $id
    ) {

        $request->validate([
            'catatan' =>
                'required|string|min:5'
        ]);


        $verifier =
            auth()->guard()->user();

        $submission =
            Submission::with([
                'user',
                'department'
            ])->findOrFail(
                $id
            );

        $submission->update([

            'status' =>
                'revisi',

            'catatan' =>
                $request->catatan,

            'revisi_at' =>
                now(),

            'tolak_by' =>
                $verifier->id
        ]);

        $additionalData = [

            'submission_id' => $submission->id,

            'verifier_nama' =>
                $verifier->name,

            'nama_dinas' =>
                $submission
                    ->department
                    ->nama_dinas
                ?? '-',

            'nomor_spm' =>
                $submission->nomor_spm,

            'nomor_spp' =>
                $submission->nomor_spp,

            'total_pencairan' =>
                $submission->total_pencairan,

            'catatan_tolak' =>
                $request->catatan,

            'status' =>
                'revisi'
        ];

        if (
            $submission->user
        ) {

            $submission->user->notify(
                new SystemActivityNotification(

                    'Pengajuan Ditolak',

                    'SPM #' .
                    $submission->nomor_spm .
                    ' ditolak. Catatan: "' .
                    $request->catatan .
                    '". Instruksi: Perbaiki dokumen yang terlampir.',

                    'fa-undo',

                    'danger',

                    $additionalData
                )
            );
        }

        return redirect()
            ->route(
                'verifikasi.index'
            )
            ->with(
                'error',
                'Pengajuan ditolak.'
            );
    }
}
