<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use App\Exports\HistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{

    // Method: jenisDokumenBertahap
    private function jenisDokumenBertahap(): array
    {
        return [
            'LS',
            'GU',
            'TU',
            'UP',
        ];
    }

    // Method: normalizeJenisPencairan
    private function normalizeJenisPencairan(?string $jenis): string
    {
        $jenis = strtoupper(trim((string) $jenis));
        $jenis = preg_replace('/\s+/', ' ', $jenis);

        return match ($jenis) {
            'TU NIHIL', 'TU-NIHIL', 'TU_NIHIL' => 'TU NIHIL',
            'GU NIHIL', 'GU-NIHIL', 'GU_NIHIL' => 'GU NIHIL',
            'LS' => 'LS',
            'TU' => 'TU',
            'GU' => 'GU',
            'UP' => 'UP',
            default => $jenis,
        };
    }

    // Method: normalizeDocumentType
    private function normalizeDocumentType($document): string
    {
        $jenis = strtoupper(trim((string) (
            $document->jenis_dokumen
            ?? $document->jenis_document
            ?? ''
        )));

        $jenis = str_replace(['-', '_', '/', '\\'], ' ', $jenis);
        $jenis = preg_replace('/\s+/', ' ', $jenis);

        return match (true) {
            str_contains($jenis, 'DOKUMEN TU NIHIL') => 'DOKUMEN TU NIHIL',
            str_contains($jenis, 'DOKUMEN GU NIHIL') => 'DOKUMEN GU NIHIL',
            str_contains($jenis, 'DOKUMEN LS') => 'DOKUMEN LS',
            str_contains($jenis, 'DOKUMEN TU') => 'DOKUMEN TU',
            str_contains($jenis, 'DOKUMEN GU') => 'DOKUMEN GU',
            str_contains($jenis, 'DOKUMEN UP') => 'DOKUMEN UP',
            str_contains($jenis, 'SP2D') => 'SP2D',
            str_contains($jenis, 'SPJ') => 'DOKUMEN SPJ',
            str_contains($jenis, 'STS') => 'DOKUMEN STS',
            default => $jenis,
        };
    }

    // Method: documentMatchesType
    private function documentMatchesType($document, string $requiredType): bool
    {
        return $this->normalizeDocumentType($document) === $requiredType;
    }

    // Method: requiredFinalDocuments
    private function requiredFinalDocuments(Submission $submission): array
    {
        return match ($this->normalizeJenisPencairan($submission->jenis_pencairan)) {
            'LS' => ['DOKUMEN LS', 'DOKUMEN SPJ', 'SP2D'],
            'TU' => ['DOKUMEN TU', 'SP2D'],
            'GU' => ['DOKUMEN GU', 'SP2D'],
            'UP' => ['DOKUMEN UP', 'SP2D'],
            'TU NIHIL' => ['DOKUMEN TU NIHIL', 'DOKUMEN SPJ', 'SP2D'],
            'GU NIHIL' => ['DOKUMEN SPJ'],
            default => [],
        };
    }

// Method: isHistoryComplete
private function isHistoryComplete(Submission $submission): bool
{
    if ($submission->status !== 'disetujui') {
        return false;
    }

    $documents = $submission->documents;

    if ($documents->isEmpty()) {
        return false;
    }

    $requiredTypes = $this->requiredFinalDocuments($submission);

    if (empty($requiredTypes)) {
        return false;
    }

    foreach ($requiredTypes as $requiredType) {

        $existsVerified = $documents->contains(function ($document) use ($requiredType) {
            return $this->documentMatchesType($document, $requiredType)
                && $document->status_dokumen === 'verified';
        });

        if (!$existsVerified) {
            return false;
        }
    }

    return true;
}

  // Method: applyHistoryDocumentFilter
  private function applyHistoryDocumentFilter($query)
{
    $verified = 'verified';

    $query->where(function ($q) use ($verified) {

        $q->where(function ($type) use ($verified) {
            $type->where('jenis_pencairan', 'LS')
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%DOKUMEN LS%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SPJ%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SP2D%')
                      ->where('status_dokumen', $verified);
                });
        })

        ->orWhere(function ($type) use ($verified) {
            $type->where('jenis_pencairan', 'GU')
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%DOKUMEN GU%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SP2D%')
                      ->where('status_dokumen', $verified);
                });
        })

        ->orWhere(function ($type) use ($verified) {
            $type->where('jenis_pencairan', 'TU')
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%DOKUMEN TU%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SP2D%')
                      ->where('status_dokumen', $verified);
                });
        })

        ->orWhere(function ($type) use ($verified) {
            $type->where('jenis_pencairan', 'GU Nihil')
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SPJ%')
                      ->where('status_dokumen', $verified);
                });
        })

        ->orWhere(function ($type) use ($verified) {
            $type->where('jenis_pencairan', 'TU Nihil')
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%DOKUMEN TU NIHIL%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SPJ%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SP2D%')
                      ->where('status_dokumen', $verified);
                });
        })

        ->orWhere(function ($type) use ($verified) {
            $type->where('jenis_pencairan', 'UP')
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%DOKUMEN UP%')
                      ->where('status_dokumen', $verified);
                })
                ->whereHas('documents', function ($d) use ($verified) {
                    $d->where('jenis_dokumen', 'like', '%SP2D%')
                      ->where('status_dokumen', $verified);
                });
        });
    });

    return $query;
}

    // Method: applyFilters
    private function applyFilters(Request $request)
    {

        $query = Submission::where(
            'status',
            'disetujui'
        );

        if ($request->filled('search')) {

            $search = $request->search;

            $cleanSearchNumber = str_replace(
                ['.', ','],
                '',
                $search
            );

            $query->where(function ($q) use (
                $search,
                $cleanSearchNumber
            ) {

                $q->where(
                    'nomor_spp',
                    'LIKE',
                    "%{$search}%"
                )
                ->orWhere(
                    'nomor_spm',
                    'LIKE',
                    "%{$search}%"
                )
                ->orWhere(
                    'judul_kegiatan',
                    'LIKE',
                    "%{$search}%"
                )
                ->orWhere(
                    'total_pencairan',
                    'LIKE',
                    "%{$search}%"
                )
                ->orWhere(
                    'total_pencairan',
                    'LIKE',
                    "%{$cleanSearchNumber}%"
                )

                ->orWhereHas(
                    'department',
                    function ($d) use ($search) {

                        $d->where(
                            'nama_dinas',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'kode_dinas',
                            'LIKE',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        if ($request->filled('date')) {

            $query->whereDate(
                'updated_at',
                $request->date
            );
        }

        if ($request->filled('jenis_pencairan')) {

            $query->where(
                'jenis_pencairan',
                $request->jenis_pencairan
            );
        }

        $sp2dStatus = $request->input('status_sp2d');

        if ($sp2dStatus === 'not_required') {
            $query->whereRaw(
                "UPPER(TRIM(COALESCE(jenis_pencairan, ''))) = ?",
                ['GU NIHIL']
            );
        } elseif (in_array($sp2dStatus, ['not_uploaded', 'pending', 'revision', 'verified'], true)) {
            $query->whereRaw(
                "UPPER(TRIM(COALESCE(jenis_pencairan, ''))) <> ?",
                ['GU NIHIL']
            );

            if ($sp2dStatus === 'not_uploaded') {
                $query->whereDoesntHave('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%');
                });
            } else {
                $query->whereHas('documents', function ($document) use ($sp2dStatus) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%');

                    if ($sp2dStatus === 'verified') {
                        $document->where('status_dokumen', 'verified');
                    } elseif ($sp2dStatus === 'revision') {
                        $document->where('status_dokumen', 'revisi');
                    } else {
                        $document->whereNotIn('status_dokumen', ['verified', 'revisi']);
                    }
                });
            }
        }

        return $query;
    }

    // Method: index
    public function index(Request $request)
    {
        $query = $this->applyFilters($request);

        $histories = $query
            ->with([
                'department',
                'user',
                'documents',
            ])
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString()
            ->onEachSide(1);

        return view(
            'history.index',
            compact('histories')
        );
    }

    // Method: exportExcel
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new HistoryExport($request),
            'Arsip_Dokumen_' .
            now()->format('d_M_Y') .
            '.xlsx'
        );
    }

    // Method: printPDF
    public function printPDF(Request $request)
    {
        $histories = $this->applyFilters($request)
            ->with([
                'department',
                'documents',
            ])
            ->get();

        $pdf = Pdf::loadView(
            'exports.history_pdf',
            compact('histories')
        )->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->stream(
            'Laporan_Arsip_Digital.pdf'
        );
    }

    // Method: show
    public function show($id)
    {
        $submission = Submission::with([
            'department',
            'user',
            'documents',
            'penolakKabid',
            'penolak',
        ])->findOrFail($id);

        if ($submission->status !== 'disetujui') {
            abort(404, 'Pengajuan belum disetujui.');
        }

        return view(
            'history.show',
            compact('submission')
        );
    }
}
