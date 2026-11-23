<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Department;

class BpkController extends Controller
{

   // Method: index
   public function index(Request $request)
{
    $query = Submission::with(['department', 'documents'])
        ->where(function ($submission) {
            $submission->whereIn('status', ['disetujui', 'approved'])
                ->orWhereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->whereIn('status_dokumen', ['verified', 'approved']);
                });
        });

    $tahun = $request->get('tahun', date('Y'));
    $query->whereYear('tanggal', $tahun);

    if ($request->filled('tanggal')) $query->whereDate('tanggal', $request->tanggal);

    if ($request->filled('nomor')) {
        $nomor = $request->nomor;
        $query->where(function($q) use ($nomor) {
            $q->where('nomor_spp', 'like', '%' . $nomor . '%')
              ->orWhere('nomor_spm', 'like', '%' . $nomor . '%');
        });
    }

    if ($request->filled('dinas')) $query->where('department_id', $request->dinas);
    if ($request->filled('min_nominal')) $query->where('total_pencairan', '=', $request->min_nominal);

    $sortField = $request->get('sort');
    $order = $request->get('order', 'desc');

    if ($sortField == 'nominal') {
        $query->orderBy('total_pencairan', $order);
    }
    elseif ($sortField == 'dinas') {
        $query->join('departments', 'submissions.department_id', '=', 'departments.id')
            ->select('submissions.*')
            ->orderBy('departments.nama_dinas', $order);
    }
    elseif ($sortField == 'nomor') {
        $query->orderBy('nomor_spm', $order)
              ->orderBy('nomor_spp', $order);
    }
    elseif ($sortField == 'tanggal' || $sortField == 'created_at') {
        $query->orderBy('created_at', $order);
    }
    elseif ($sortField == 'status') {
        $query->orderBy('status', $order);
    }
    else {
        $query->orderBy('created_at', 'desc');
    }

    $submissions = $query->paginate(10)->withQueryString();

    $departments = Department::all();

    return view('bpk.monitoring', compact('submissions', 'departments'));
}

// Method: show
public function show($id)
{
    $submission = Submission::with(['department', 'documents'])->findOrFail($id);

    $previous = Submission::where('id', '<', $id)
        ->where(function ($eligible) {
            $eligible->whereIn('status', ['disetujui', 'approved'])
                ->orWhereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->whereIn('status_dokumen', ['verified', 'approved']);
                });
        })
        ->orderBy('id', 'desc')
        ->first();

    $next = Submission::where('id', '>', $id)
        ->where(function ($eligible) {
            $eligible->whereIn('status', ['disetujui', 'approved'])
                ->orWhereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->whereIn('status_dokumen', ['verified', 'approved']);
                });
        })
        ->orderBy('id', 'asc')
        ->first();

    return view('bpk.show', compact('submission', 'previous', 'next'));
}

// Method: dashboard
public function dashboard()
{
    $statusCounts = Submission::select('status', \DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status');

    $totalStats = Submission::selectRaw('count(*) as total_berkas, sum(total_pencairan) as total_nilai')->first();

    $stats = [
        'total_berkas'    => $totalStats->total_berkas,
        'total_nilai'     => $totalStats->total_nilai,
        'pending'         => $statusCounts->get('pending', 0),
        'proses_validasi' => $statusCounts->get('proses validasi', 0),
        'disetujui'       => $statusCounts->get('disetujui', 0),
        'ditolak'         => $statusCounts->get('ditolak', 0),
        'revisi'          => $statusCounts->get('revisi', 0),
        'direvisi'        => $statusCounts->get('direvisi', 0),
        'rejected'        => $statusCounts->get('rejected', 0),
        'reset'           => $statusCounts->get('reset', 0),
        'verified'        => $statusCounts->get('verified', 0),
    ];

    $sp2dBelumUploadQuery = Submission::query()
        ->whereIn('status', ['disetujui', 'verified'])
        ->whereRaw("UPPER(TRIM(COALESCE(jenis_pencairan, ''))) <> ?", ['GU NIHIL'])
        ->whereDoesntHave('documents', function ($document) {
            $document->where('jenis_dokumen', 'LIKE', '%SP2D%');
        });

    $stats['sp2d_belum_upload'] = (clone $sp2dBelumUploadQuery)->count();

    $sp2dBelumUpload = (clone $sp2dBelumUploadQuery)
        ->with('department')
        ->latest('updated_at')
        ->paginate(10, ['*'], 'sp2d_page')
        ->withQueryString();

    $recentSubmissions = Submission::with(['department', 'user', 'documents'])
        ->where(function ($submission) {
            $submission->whereIn('status', ['disetujui', 'approved'])
                ->orWhereHas('documents', function ($document) {
                    $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                        ->whereIn('status_dokumen', ['verified', 'approved']);
                });
        })
        ->latest()
        ->take(10)
        ->get();

    return view('bpk.dashboard', compact('stats', 'recentSubmissions', 'sp2dBelumUpload'));
}

// Method: submitAudit
public function submitAudit(Request $request, $id)
    {
        $request->validate([
            'status_audit' => 'required|in:sesuai,dengan_catatan',
            'catatan_audit' => 'required_if:status_audit,dengan_catatan|nullable|string|max:1000'
        ], [
            'catatan_audit.required_if' => 'Catatan temuan wajib diisi jika Anda memilih status Dengan Catatan.'
        ]);

        $submission = Submission::findOrFail($id);

        $submission->status_audit = $request->status_audit;

        $submission->catatan_audit = ($request->status_audit === 'sesuai') ? null : $request->catatan_audit;

        $submission->save();

        return redirect()->back()->with('success', 'Hasil keputusan audit pengajuan berhasil disimpan!');
    }
}
